<?php

namespace SebastienHeyd\HiddenCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

class HiddenCaptcha extends Facade
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
