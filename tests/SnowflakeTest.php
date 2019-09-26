<?php

namespace Kra8\Snowflake\Test;

use Illuminate\Support\Facades\Config;
use Kra8\Snowflake\Snowflake;
use Kra8\Snowflake\SnowflakeServiceProvider;

class SnowflakeTest extends AbstractTestCase
{
    public function testNextId()
    {
        $now    = strtotime(date('Y-m-d H:i:s'));
        $epoch  = strtotime(Config::get('snowflake.epoch')) * 1000;
        $id     = resolve(Snowflake::class)->next();

        $timestamp = $id >> 22;
        $timestamp = (int) round(($timestamp + $epoch) / 1000);

        $this->assertTrue($timestamp - $now < 3);
    }

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
            'epoch'         => '2017-10-13 00:00:00',
            'worker_id'     => '1',
            'datacenter_id' => '1',
        ]);
    }
}
