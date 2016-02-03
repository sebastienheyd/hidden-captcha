<?php namespace SebastienHeyd\HiddenCaptcha;

use Illuminate\Support\ServiceProvider;

class HiddenCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('hiddencaptcha', function ($attribute, $value, $parameters, $validator) {
            return HiddenCaptcha::check($validator->getData()[$attribute]);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['hiddencaptcha'] = $this->app->share(function () {
            return new HiddenCaptcha();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('hiddencaptcha');
    }
}
