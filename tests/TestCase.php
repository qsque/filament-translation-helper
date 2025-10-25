<?php

namespace Qsque\FilamentTranslationHelper\Tests;

use Qsque\FilamentTranslationHelper\FilamentTranslationHelperServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FilamentTranslationHelperServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }
}