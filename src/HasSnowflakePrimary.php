<?php
namespace Kra8\Snowflake;

use Kra8\Snowflake\Snowflake;

trait HasSnowflakePrimary
{
    public static function bootHasSnowflakePrimary()
    {
        static::saving(function ($model) {
            if (is_null($model->getKey())) {
                $model->setIncrementing(false);
                $keyName    = $model->getKeyName();
                $id         = app(Snowflake::class)->next();
                $model->setAttribute($keyName, $id);
            }
        });
    }
}
