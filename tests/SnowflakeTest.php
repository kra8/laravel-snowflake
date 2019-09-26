<?php

namespace Kra8\Snowflake\Test;

use Kra8\Snowflake\Snowflake;
use Kra8\Snowflake\SnowflakeServiceProvider;

class SnowflakeTest extends AbstractTestCase
{
    public function testNextId()
    {
        $now    = strtotime(date('Y-m-d H:i:s'));
        $epoch  = strtotime(config('snowflake.epoch')) * 1000;
        $id     = app(Snowflake::class)->next();

        $timestamp = $id >> 22;
        $timestamp = (int) round(($timestamp + $epoch) / 1000);

        $this->assertTrue($timestamp - $now < 3);
    }
}
