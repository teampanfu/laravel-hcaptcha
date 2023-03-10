<?php

namespace Panfu\Laravel\HCaptcha;

use Illuminate\Support\ServiceProvider;

class HCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/hcaptcha.php' => config_path('hcaptcha.php'),
        ]);

        $this->app->validator->extend('hcaptcha', function ($attribute, $value) {
            return $this->app->hcaptcha->validate($value, request()->ip());
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/hcaptcha.php', 'hcaptcha'
        );

        $this->app->singleton('hcaptcha', function () {
            return new HCaptcha(
                config('hcaptcha.sitekey'),
                config('hcaptcha.secret'),
            );
        });
    }
}
