<?php namespace SebastienHeyd\HiddenCaptcha;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class HiddenCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('hiddencaptcha', function ($mustBeEmptyField = '_username') {
            return "<?= HiddenCaptcha::render($mustBeEmptyField); ?>";
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
        $this->publishes([__DIR__.'/views' => resource_path('views/vendor/hiddenCaptcha')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Facade
        $this->app->bind('hiddencaptcha', 'SebastienHeyd\HiddenCaptcha\HiddenCaptcha');
    }
}
