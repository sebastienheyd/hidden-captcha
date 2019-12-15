<?php

namespace SebastienHeyd\HiddenCaptcha\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SebastienHeyd\HiddenCaptcha\ServiceProvider;

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
            ServiceProvider::class,
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->app['validator'];
        \Session::start();
    }
}
