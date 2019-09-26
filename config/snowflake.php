<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Snowflake Epoch
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for snowflake. Set the date
    | the application was develop started. Don't set the date of the future.
    | If service starts to move, don't change.
    |
    | Available Settings: Y-m-d H:i:s
    |
    */
    'epoch' => env('SNOWFLAKE_EPOCH', '2019-04-01 00:00:00'),

    /*
    |--------------------------------------------------------------------------
    | Snowflake Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for snowflake.
    | If you are using multiple servers, please assign unique
    | ID(1-31) for Snowflake.
    |
    | Available Settings: 1-31
    |
    */
    'worker_id' => env('SNOWFLAKE_WORKER_ID', 1),

    'datacenter_id' => env('SNOWFLAKE_DATACENTER_ID', 1),
];
