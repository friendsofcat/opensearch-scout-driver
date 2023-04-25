# OpenSearch Scout Driver

OpenSearch driver for Laravel Scout.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Basic Usage](#basic-usage)
* [Advanced Search](#advanced-search)
* [Migrations](#migrations)
* [Pitfalls](#pitfalls)

## Compatibility

The current version of OpenSearch Scout Driver has been tested with the following configuration:

* PHP 7.4-8.2
* OpenSearch 2.x
* Laravel 7.x-10.x
* Laravel Scout 7.x-10.x

## Installation

The library can be installed via Composer:

```bash
composer require friendsofcat/opensearch-scout-driver
```

**Note**, that this library is just a driver for Laravel Scout, don't forget to install it beforehand:
```bash
composer require laravel/scout
```

After Scout has been installed, publish its configuration file using:

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

Then, change the `driver` option in the `config/scout.php` file to `opensearch`:

```php
// config/scout.php

'driver' => env('SCOUT_DRIVER', 'opensearch'),
```

If you want to use OpenSearch Scout Driver with [Lumen framework](https://lumen.laravel.com/) check [this guide](https://github.com/babenkoivan/elastic-scout-driver/wiki/Lumen-Installation).

## Configuration

OpenSearch Scout Driver uses [friendsofcat/opensearch-client](https://github.com/friendsofcat/opensearch-client) as a dependency.
To change the client settings you need to publish the configuration file first:

```bash
php artisan vendor:publish --provider="OpenSearch\Laravel\Client\ServiceProvider"
```

In the newly created `config/opensearch.client.php` file you can define the default connection name using configuration hashes.
Please, refer to the [opensearch-client documentation](https://github.com/friendsofcat/opensearch-client) for more details.

OpenSearch Scout Driver itself has only one configuration option at the moment - `refresh_documents`.
If it's set to `true` (`false` by default) documents are indexed immediately, which might be handy for testing.

You can configure `refresh_documents` in the `config/opensearch.scout_driver.php` file after publishing it with the following command:

```bash
php artisan vendor:publish --provider="OpenSearch\ScoutDriver\ServiceProvider"
```

At last, do not forget, that with Scout you can configure the searchable data, the model id and the index name.
Check [the official Scout documentation](https://laravel.com/docs/master/scout#configuration) for more details.

> Note, that the `_id` field can't be part of the searchable data, so make sure the field is excluded or renamed
> in the `toSearchableArray` method in case you are using MongoDB as the database.

## Basic usage

OpenSearch driver uses OpenSearch [query string](https://opensearch.org/docs/1.3/opensearch/query-dsl/index/)
wrapped in a [bool query](https://opensearch.org/docs/1.3/opensearch/query-dsl/bool/)
under the hood. It means that you can use [mini-language syntax](https://opensearch.org/docs/1.3/opensearch/query-dsl/full-text/)
when searching a model:

```php
$orders = App\Order::search('title:(Star OR Trek)')->get();
```

When the query string is omitted, the [match all query](https://opensearch.org/docs/1.3/opensearch/query-dsl/full-text/#match-all)
is used:
```php
$orders = App\Order::search()->where('user_id', 1)->get();
```

Please refer to [the official Laravel Scout documentation](https://laravel.com/docs/master/scout)
for more details and usage examples.

## Advanced Search

In case the basic search doesn't cover your project needs check [OpenSearch Scout Driver Plus](https://github.com/friendsofcat/opensearch-scout-driver-plus),
which extends standard Scout search capabilities by introducing advanced query builders. These builders give you
possibility to use compound queries, custom filters and sorting, highlights and more.

## Migrations

If you are looking for a way to control OpenSearch index schema programmatically check [OpenSearch Migrations](https://github.com/friendsofcat/opensearch-migrations).
OpenSearch Migrations allow you to modify application's index schema and share it across multiple environments with the same ease,
that gives you Laravel database migrations.

## Pitfalls

There are few things, which are slightly different from other Scout drivers:
* As you probably know, Scout only indexes fields, which are returned by the `toSearchableArray` method.
OpenSearch driver indexes a model even when `toSearchableArray` returns an empty array. You can change this behaviour by
overwriting the `shouldBeSearchable` method of your model:
```php
public function shouldBeSearchable()
{
    return count($this->toSearchableArray()) > 0;
}
```
* Raw search returns an instance of `SearchResult` class (see [OpenSearch Adapter](https://github.com/friendsofcat/opensearch-adapter#search)):
```php
$searchResult = App\Order::search('Star Trek')->raw();
```
* To be compatible with other drivers and to not expose internal implementation of the engine, OpenSearch driver ignores callback
parameter of the `search` method:
```php
App\Order::search('Star Trek', function () {
    // this will not be triggered
})->get()
```
