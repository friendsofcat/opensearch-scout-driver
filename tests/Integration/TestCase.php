<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Tests\Integration;

use Illuminate\Config\Repository;
use Laravel\Scout\ScoutServiceProvider;
use OpenSearch\Laravel\Client\ServiceProvider as OpenSearchClientServiceProvider;
use OpenSearch\Migrations\ServiceProvider as OpenSearchMigrationsServiceProvider;
use OpenSearch\ScoutDriver\ServiceProvider as OpenSearchScoutDriverServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected Repository $config;

    protected function getPackageProviders($app)
    {
        return [
            ScoutServiceProvider::class,
            OpenSearchClientServiceProvider::class,
            OpenSearchMigrationsServiceProvider::class,
            OpenSearchScoutDriverServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->config = $app['config'];
        $this->config->set('scout.driver', 'opensearch');
        $this->config->set('opensearch.migrations.storage.default_path', dirname(__DIR__) . '/App/opensearch/migrations');
        $this->config->set('opensearch.scout_driver.refresh_documents', true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__) . '/App/database/migrations');
        $this->withFactories(dirname(__DIR__) . '/App/database/factories');

        $this->artisan('migrate')->run();
        $this->artisan('opensearch:migrate')->run();
    }

    protected function tearDown(): void
    {
        $this->artisan('opensearch:migrate:reset')->run();
        $this->artisan('migrate:reset')->run();

        parent::tearDown();
    }
}
