# Laravel Snowflake
This Laravel package to generate 64 bit identifier like the snowflake within Twitter.

# Installation
```
composer require "kra8/laravel-snowflake:^1.0"

php artisan vendor:publish --provider="Kra8\Snowflake\SnowflakeServiceProvider"
```
# Usage
Get instance
``` php
$snowflake = $this->app->make('Kra8\Snowflake\Snowflake');
```
or
``` php
$snowflake = resolve('Kra8\Snowflake\Snowflake');
```

Generate snowflake identifier
```
$id = $snowflake->next();
```
# Usage with Eloquent
Add the `Kra8\Snowflake\HasSnowflakePrimary` trait to your Eloquent model.
This trait make type `snowflake` of primary key.  Don't forget to set the Auto increment property to false.

``` php
<?php
namespace App;

use Kra8\Snowflake\HasSnowflakePrimary;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasSnowflakePrimary, Notifiable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Finally, in migrations, set the primary key to `bigInteger` and `primary`.

``` php
/**
 * Run the migrations.
 *
 * @return void
 */
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        // $table->increments('id');
        $table->bigInteger('id')->primary();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```


# Configuration
If `config/snowflake.php` not exist, run below:
```
php artisan vendor:publish
```

# Licence
[MIT licence](https://github.com/kra8/laravel-snowflake/blob/master/LICENSE)
