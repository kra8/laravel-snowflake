<?php

namespace Kra8\Snowflake\Test;

use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('snowflake', [
            'epoch'         => '2017-01-01 00:00:00',
            'worker_id'     => '1',
            'datacenter_id' => '1',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return ['Kra8\Snowflake\Providers\LaravelServiceProvider'];
    }
}