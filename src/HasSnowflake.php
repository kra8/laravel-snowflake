<?php
namespace Kra8\Snowflake;

use Kra8\Snowflake\Snowflake;

trait HasSnowflake
{
    public static function bootHasSnowflake()
    {
        static::saving(function ($model) {
            if (is_null($model->getKey())) {
                $keyName    = $model->getKeyName();
                $id         = resolve(Snowflake::class)->next();
                $model->setAttribute($keyName, $id);
            }
        });
    }
}
