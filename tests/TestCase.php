<?php namespace SebastienHeyd\HiddenCaptcha\Tests;

use SebastienHeyd\HiddenCaptcha\HiddenCaptchaServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            HiddenCaptchaServiceProvider::class
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
            'HiddenCaptcha' => \SebastienHeyd\HiddenCaptcha\HiddenCaptchaFacade::class
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->app['validator'];
    }
}