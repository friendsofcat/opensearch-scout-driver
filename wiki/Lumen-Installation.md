# Lumen Installation

Note, that the package wasn't designed to be used with [Lumen framework](https://lumen.laravel.com/), therefore it's not guaranteed, that you won't encounter any bugs or side effects. If you want to proceed anyway, follow the instructions below:

* Add missing helpers in **app/helpers.php**

```
<?php declare(strict_types=1);

if (!function_exists('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('resolve')) {
    function resolve($name, array $parameters = [])
    {
        return app($name, $parameters);
    }
}
```

* Enable **helpers.php** autoload in **composer.json**

```
"autoload": {
    // ...,
    "files": [
        "app/helpers.php"
    ]
}
```

* Install the package and Laravel Scout via Composer

```
composer require friendsofcat/opensearch-scout-driver laravel/scout
```

* Make the **config** folder

```
mkdir config
```

* Copy the configuration files

```
cp vendor/laravel/scout/config/scout.php config/scout.php
cp vendor/friendsofcat/opensearch-client/config/opensearch.client.php config/opensearch.client.php
cp vendor/friendsofcat/opensearch-scout-driver/config/opensearch.scout_driver.php config/opensearch.scout_driver.php
```

* Register container bindings under the _Register Container Bindings_ section in **bootstrap/app.php**

```
$app->instance('path.config', app()->basePath() . '/config');

$app->withEloquent();
$app->withFacades();
```

* Register service providers under the _Register Service Providers_ section in **bootstrap/app.php**

```
$app->register(Laravel\Scout\ScoutServiceProvider::class);
$app->register(OpenSearch\Laravel\Client\ServiceProvider::class);
$app->register(OpenSearch\ScoutDriver\ServiceProvider::class);
```

* Enable _opensearch_ driver in **config/scout.php**

* Configure OpenSearch client in **config/opensearch.client.php**
