<?php

namespace Kwaadpepper\ExceptionHandler\Exceptions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExceptionHandler extends Handler
{
    /**
     * Create a new exception handler instance.
     *
     * @param  \Illuminate\Contracts\Container\Container $container
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->reportable($this->reportExceptionByEmail());
    }

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     * @return void
     */
    public function report(\Throwable $e)
    {
        if (get_class($e) == RedirectTo::class) {
            abort(redirect($e->url));
        }
        parent::report($e);
    }

    /**
     * Manually send exception by email
     *
     * @param \Throwable $e
     * @return void
     */
    public static function sendExceptionByEmailStatic(\Throwable $e)
    {
        app(self::class)->sendExceptionByEmail($e);
    }

    /**
     * Manually send exception by email
     *
     * @param \Throwable $e
     * @return void
     */
    public function sendExceptionByEmail(\Throwable $e)
    {
        $this->reportExceptionByEmail()($e);
    }

    /**
     * Report sending mail closure
     *
     * @return \Closure
     */
    private function reportExceptionByEmail(): \Closure
    {
        return function (\Throwable $e) {
            if ($this->shouldntReport($e)) {
                return false;
            }
            // * Ignore NoContactException.
            if ($e instanceof NoContactException) {
                Log::critical($e->getMessage());
                return false;
            }
            $dest = \collect(config('exception-handler.contactsList', config('mail.from.address')))->filter();
            try {
                if (!config('exception-handler.enableMaiLog')) {
                    return false;
                }
                if (!$dest->count()) {
                    throw new NoContactException(
                        'No contact in `EXCEPTION_MAIL_LIST` nor `exception-handler.contactsList` config',
                        0,
                        $e
                    );
                }
                Mail::send([
                    'html' => 'exception-handler::emails.exception',
                    'text' => 'exception-handler::emails.exception-plain'
                ], [
                    'appName' => config('app.name'),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'ename' => \get_class($e),
                    'emessage' => self::getAnonymizedMessage($e->getMessage()),
                    'file' => str_replace(\realpath(\sprintf('%s/../', \app_path())), '', $e->getFile()),
                    'stackTrace' => self::getAnonymizedStackTrace($e),
                    'uri' => app()->runningInConsole() ?
                        'CONSOLE' : (
                            (request() and request()->fullUrl()) ?
                            request()->fullUrl() : 'none')
                ], function ($m) use ($dest) {
                    /** @var \Illuminate\Mail\Message $m */
                    $m->to(
                        $dest->all(),
                        sprintf('%s Exception Handler', config('app.name'))
                    )->subject('Need help !, an error occured in your application, please check Logs ASAP');
                });
            } catch (\Error $reportE) {
                parent::report($e);
                Log::critical('Handler could not send Exception email', ['exception' => $reportE]);
            }//end try
        };
    }

    /**
     * Anonymize at least Sql queries
     *
     * @param string $message
     * @return string
     */
    private static function getAnonymizedMessage(string $message): string
    {
        $message = \preg_replace('/^(.*)( \(SQL:.*\))$/', '$1', $message) ?? $message;
        if ($sqlPos = \strpos($message, 'to use near')) {
            $message = \mb_strcut($message, 0, $sqlPos);
        }
        return $message;
    }

    /**
     * Anonymize stack trace
     *
     * @param \Throwable $e
     * @return string
     */
    private static function getAnonymizedStackTrace(\Throwable $e): string
    {
        $projectPath   = \realpath(\sprintf('%s/../', \app_path()));
        $traceStack    = $e->getTrace();
        $traceStr      = '';
        $maxStackStage = 0;
        foreach ($traceStack as $stackStage => $stack) {
            $maxStackStage = $stackStage;
            $traceStr     .= \sprintf(
                '#%s %s(%d): ',
                $stackStage,
                str_replace($projectPath, '', $stack['file'] ?? ''),
                $stack['line'] ?? ''
            );
            if (isset($stack['class'])) {
                $traceStr .= "{$stack['class']}->{$stack['function']}(";
                if (isset($stack['args'])) {
                    $traceStr .= self::handleArgs($stack);
                }
                $traceStr .= ")\n";
            } elseif (isset($stack['function'])) {
                $traceStr .= "{$stack['function']}(";
                if (isset($stack['args'])) {
                    $traceStr .= self::handleArgs($stack);
                }
                $traceStr .= ")\n";
            }
        } //end foreach
        $maxStackStage++;
        $traceStr .= "#$maxStackStage {main}";
        return $traceStr;
    }

    /**
     * Handle Args
     *
     * @param array $stack
     * @return string
     */
    private static function handleArgs(array $stack): string
    {
        $traceArgs = '';
        $argsCount = count($stack['args']);
        for ($i = 0; $i < $argsCount; $i++) {
            $type = \gettype($stack['args'][$i]);
            switch ($type) {
                case 'object':
                    $traceArgs .= sprintf('Object(%s)', \get_class($stack['args'][$i]));
                    break;
                case 'array':
                    $traceArgs .= sprintf('Array(%d)', \count($stack['args'][$i]));
                    break;
                case 'string':
                    $traceArgs .= sprintf('String(%d)', \strlen($stack['args'][$i]));
                    break;
                default:
                    $traceArgs .= \gettype($stack['args'][$i]);
            }
            $traceArgs .= ($i !== ($argsCount - 1) ? ',' : '');
        }
        return $traceArgs;
    }
}
