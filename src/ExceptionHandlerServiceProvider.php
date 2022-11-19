<?php

namespace Kwaadpepper\ExceptionHandler;

use Illuminate\Support\ServiceProvider;

class ExceptionHandlerServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Kwaadpepper\ExceptionHandler\Console\Commands\InstallHandler',
        'Kwaadpepper\ExceptionHandler\Console\Commands\RemoveHandler'
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang/');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'exception-handler');

        $this->publishes([
            __DIR__ . '/../config' => config_path(),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/exception-handler.php',
            'exception-handler'
        );
        $this->commands($this->commands);
    }
}
