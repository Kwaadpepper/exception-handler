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
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        if (
            \strpos(app()->version(), '8') === 0 or
            \strpos(app()->version(), '9') === 0
        ) {
            $this->reportable($this->reportExceptionByEmail());
        }
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(\Throwable $e)
    {
        if (get_class($e) == RedirectTo::class) {
            abort(redirect($e->url));
        }
        // ? If in laravel 7 and and error occurs when sending mail.
        if (
            \strpos(app()->version(), '7') === 0 and
            !$this->reportExceptionByEmail()($e)
        ) {
            return false;
        }
        parent::report($e);
    }

    /**
     * Report sending mail closure
     *
     * @return \Closure
     */
    private function reportExceptionByEmail(): \Closure
    {
        return function (\Throwable $e) {
            $dest = config('exception-handler.contactsList', config('mail.from.address'));
            try {
                if ($dest and config('exception-handler.enableMaiLog')) {
                    if ($this->shouldntReport($e)) {
                        return false;
                    }
                    Mail::send([
                        'html' => 'exception-handler::emails.exception',
                        'text' => 'exception-handler::emails.exception-plain'
                    ], [
                        'appName' => config('app.name'),
                        'code' => $e->getCode(),
                        'line' => $e->getLine(),
                        'ename' => \get_class($e),
                        'emessage' => $this->getAnonymizedMessage($e->getMessage()),
                        'file' => str_replace(\realpath(\sprintf('%s/../', \app_path())), '', $e->getFile()),
                        'stackTrace' => $this->getAnonymizedStackTrace($e),
                        'uri' => app()->runningInConsole() ?
                            'CONSOLE' : (
                                (request() and request()->fullUrl()) ?
                                request()->fullUrl() : 'none')
                    ], function ($m) use ($dest) {
                        // ? Is laravel 9 (https://laravel.com/docs/master/upgrade#symfony-mailer)
                        if(\strpos(app()->version(), '9') !== 0) {
                            /** @var \Illuminate\Mail\Message $m */
                            $m->setTo(
                                $dest,
                                sprintf('%s Exception Handler', config('app.name'))
                            )->setSubject('Need help !, an error occured in your application, please check Logs ASAP');
                        } else {
                            /** @var \Illuminate\Mail\Message $m */
                            $m->to(
                                $dest,
                                sprintf('%s Exception Handler', config('app.name'))
                            )->subject('Need help !, an error occured in your application, please check Logs ASAP');
                        }
                    });
                }
            } catch (\Error $e) {
                Log::critical('Handler could not send Exception email', ['exception' => $e]);
            }
        };
    }

    /**
     * Anonymize at least Sql queries
     *
     * @param string $message
     * @return string
     */
    private function getAnonymizedMessage(string $message): string
    {
        $message = \preg_replace('/^(.*)( \(SQL:.*\))$/', '$1', $message) ?? $message;
        return $message;
    }

    /**
     * Anonymize stack trace
     *
     * @param \Exception $e
     * @return string
     */
    private function getAnonymizedStackTrace(\Throwable $e): string
    {
        $projectPath = \realpath(\sprintf('%s/../', \app_path()));
        $traceStack = $e->getTrace();
        $traceStr = '';
        $maxStackStage = 0;
        foreach ($traceStack as $stackStage => $stack) {
            $maxStackStage = $stackStage;
            $traceStr .= \sprintf(
                '#%s %s(%d): ',
                $stackStage,
                str_replace($projectPath, '', $stack['file'] ?? ''),
                $stack['line'] ?? ''
            );
            if (isset($stack['class'])) {
                $traceStr .= "{$stack['class']}->{$stack['function']}(";
                if (isset($stack['args'])) {
                    $traceStr .= $this->handleArgs($stack);
                }
                $traceStr .= ")\n";
            } elseif (isset($stack['function'])) {
                $traceStr .= "{$stack['function']}(";
                if (isset($stack['args'])) {
                    $traceStr .= $this->handleArgs($stack);
                }
                $traceStr .= ")\n";
            }
        }
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
    private function handleArgs(array $stack): string
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
