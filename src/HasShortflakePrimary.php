<?php declare(strict_types=1);

namespace Kra8\Snowflake;

use Kra8\Snowflake\Snowflake;

trait HasShortflakePrimary
{
    public static function bootHasShortflakePrimary()
    {
        static::saving(function ($model) {
            if (is_null($model->getKey())) {
                $model->setIncrementing(false);
                $keyName    = $model->getKeyName();
                $id         = app(Snowflake::class)->short();
                $model->setAttribute($keyName, $id);
            }
        });
    }
}
