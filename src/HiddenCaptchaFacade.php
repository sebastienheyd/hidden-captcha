<?php namespace SebastienHeyd\HiddenCaptcha;

use Illuminate\Support\Facades\Facade;

class HiddenCaptchaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hiddencaptcha';
    }
}
