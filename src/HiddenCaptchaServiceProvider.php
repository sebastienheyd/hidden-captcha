<?php

namespace SebastienHeyd\HiddenCaptcha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class HiddenCaptchaServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('hiddencaptcha', function () {
            return '<?= HiddenCaptcha::render(); ?>';
        });

        $this->app['validator']->extendImplicit(
            'hiddencaptcha',
            function ($attribute, $value, $parameters, $validator) {
                $minLimit = (isset($parameters[0]) && is_numeric($parameters[0])) ? $parameters[0] : 0;
                $maxLimit = (isset($parameters[1]) && is_numeric($parameters[1])) ? $parameters[1] : 1200;
                if (!HiddenCaptcha::check($validator, $minLimit, $maxLimit)) {
                    $validator->setCustomMessages(['hiddencaptcha' => 'Captcha error']);

                    return false;
                }

                return true;
            }
        );

        $this->loadViewsFrom(__DIR__.'/views', 'hiddenCaptcha');
        $this->loadRoutesFrom(__DIR__.'/routes/hidden-captcha.php');

        $this->publishes([__DIR__.'/public' => public_path()], 'laravel-assets');
        $this->publishes([__DIR__.'/config' => config_path()], 'captcha-config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/hidden_captcha.php', 'hidden_captcha');

        // Facade
        $this->app->bind('hiddencaptcha', 'SebastienHeyd\HiddenCaptcha\HiddenCaptcha');
    }
}
