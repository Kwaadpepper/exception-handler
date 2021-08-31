<?php

namespace Kwaadpepper\ExceptionHandler\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveHandler extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handler:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the exception handler';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $handlerPath = \app_path('Exceptions/Handler.php');
        if (!File::exists($handlerPath)) {
            $this->error("The [$handlerPath] file could not be found.");
            exit;
        }
        $handlerFile = File::get($handlerPath);
        $find = 'use Kwaadpepper\ExceptionHandler\Exceptions\ExceptionHandler;';
        $replace = 'use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;';
        if (\strpos($handlerFile, $replace) !== false) {
            $this->error('Exception handler seems to already be removed');
            exit;
        }
        if (\strpos($handlerFile, $find) === false) {
            $this->error('Exception handler could not be removed, cannot find str to replace');
            exit;
        }
        $handlerFile = \str_replace($find, $replace, $handlerFile);
        File::put($handlerPath, $handlerFile);

        $source = \config_path('exception-handler.php');
        $sourceExist = File::exists($source);
        if (
            $sourceExist and
            $this->ask('Remove config/exception-handler.php ? [y/n]') === 'y'
        ) {
            File::delete($source);
        }

        $this->info('Exception handler was successfully removed');
    }
}
