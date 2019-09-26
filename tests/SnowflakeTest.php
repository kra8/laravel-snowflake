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
}
