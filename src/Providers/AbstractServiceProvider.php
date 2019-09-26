<?php

namespace Kra8\Snowflake\Providers;

use Illuminate\Support\ServiceProvider;
use Kra8\Snowflake\Snowflake;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Snowflake::class, function () {
            $epoch = config('snowflake.epoch');
            $workerId = config('snowflake.worker_id');
            $datacenterId = config('snowflake.datacenter_id');

            $timestamp = strtotime($epoch);
            return new Snowflake($timestamp, $workerId, $datacenterId);
        });
    }
}