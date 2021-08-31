<?php

namespace Kwaadpepper\ExceptionHandler\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallHandler extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handler:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the exception handler';

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
        $find = 'use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;';
        $replace = 'use Kwaadpepper\ExceptionHandler\Exceptions\ExceptionHandler;';
        if (\strpos($handlerFile, $replace) !== false) {
            $this->error('Exception handler seems to already be installed');
            exit;
        }
        if (\strpos($handlerFile, $find) === false) {
            $this->error('Exception handler could not be installed, cannot find str to replace');
            exit;
        }
        $handlerFile = \str_replace($find, $replace, $handlerFile);
        File::put($handlerPath, $handlerFile);
        $this->info("Replaced $handlerPath");

        $source = __DIR__ . '/../../../config/exception-handler.php';
        $dest = \config_path('exception-handler.php');
        $destExist = File::exists($dest);
        if (
            !$destExist or
            ($destExist and
                $this->ask('Config file exception-handler.php already exists, overwrite ? [y/n]') === 'y')
        ) {
            File::copy($source, $dest);
            $this->info("Installed config file $dest");
        }

        $this->info('Exception handler was successfully installed');
    }
}
