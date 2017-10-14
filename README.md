# Laravel Snowflake
This Laravel package to generate snowflake identifier.

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

# Configuration
If `config/snowflake.php` not exist, run below:
``` php
php artisan vendor:publish
```

# Licence
[MIT licence]()
