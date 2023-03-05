<?php

namespace Kanelli\GraphValidateCode\Providers;

use Illuminate\Support\ServiceProvider;
use Kanelli\GraphValidateCode\GraphValidateCode;

/**
 * Class ServiceProvider
 * @package Kanelli\ImageVerifyCode
 */
class GraphValidateCodeProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/validate.php' => config_path('validate.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('gvc', function () {
            return new GraphValidateCode();
        });
    }
}