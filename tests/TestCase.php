<?php

namespace SebastienHeyd\HiddenCaptcha\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            HiddenCaptchaServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'HiddenCaptcha' => \SebastienHeyd\HiddenCaptcha\Facade::class,
        ];
    }
}
