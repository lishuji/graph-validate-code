<?php

namespace Kanelli\GraphValidateCode;

use Kanelli\GraphValidateCode\Services\GraphValidateCodeServer;

/**
 * Class ServiceProvider
 * @package Kanelli\ImageVerifyCode
 */
class GraphValidateCodeProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/validate-code.php' => config_path('validate-code.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GraphValidateCodeServer::class, function () {
            return new GraphValidateCodeServer();
        });
    }
}