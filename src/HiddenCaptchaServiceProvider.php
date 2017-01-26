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
            $this->loadTranslationsFrom(__DIR__ . '/lang', 'hiddencaptcha');
            $minLimit = (isset($parameters[0]) && is_numeric($parameters[0])) ? $parameters[0] : 0;
            $maxLimit = (isset($parameters[1]) && is_numeric($parameters[1])) ? $parameters[1] : 1200;
            return HiddenCaptcha::check($validator->getData()[$attribute], $minLimit, $maxLimit, $validator);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('hiddencaptcha', 'SebastienHeyd\HiddenCaptcha\HiddenCaptcha');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['hiddencaptcha'];
    }
}
