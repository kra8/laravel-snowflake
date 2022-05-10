<?php

namespace Kra8\Snowflake\Test;

use Kra8\Snowflake\Snowflake;

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

    public function testMultipleNextId()
    {
        $instance = app(Snowflake::class);

        $ids = [];
        foreach (range(0, 1000) as $_) {
            $ids[] = $instance->next();
        }
        $this->assertTrue(array_unique($ids) === $ids);
    }

    public function testShortId()
    {
        $now    = strtotime(date('Y-m-d H:i:s'));
        $epoch  = strtotime(config('snowflake.epoch')) * 1000;
        $id     = app(Snowflake::class)->short();

        $timestamp = $id >> 12;
        $timestamp = (int) round(($timestamp + $epoch) / 1000);

        $this->assertTrue($timestamp - $now < 3);
    }

    public function testMultipleShortId()
    {
        $instance = app(Snowflake::class);
        $ids = [];
        foreach (range(0, 1000) as $_) {
            $ids[] = $instance->short();
        }
        $this->assertTrue(array_unique($ids) === $ids);
    }
}
