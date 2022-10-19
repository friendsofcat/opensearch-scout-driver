<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as AbstractServiceProvider;
use Laravel\Scout\EngineManager;
use OpenSearch\ScoutDriver\Factories\DocumentFactory;
use OpenSearch\ScoutDriver\Factories\DocumentFactoryInterface;
use OpenSearch\ScoutDriver\Factories\ModelFactory;
use OpenSearch\ScoutDriver\Factories\ModelFactoryInterface;
use OpenSearch\ScoutDriver\Factories\SearchParametersFactory;
use OpenSearch\ScoutDriver\Factories\SearchParametersFactoryInterface;

final class ServiceProvider extends AbstractServiceProvider
{
    private string $configPath;

    private array $weakBindings = [
        ModelFactoryInterface::class => ModelFactory::class,
        DocumentFactoryInterface::class => DocumentFactory::class,
        SearchParametersFactoryInterface::class => SearchParametersFactory::class,
    ];

    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->configPath = dirname(__DIR__) . '/config/opensearch.scout_driver.php';
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->configPath,
            basename($this->configPath, '.php')
        );

        foreach ($this->weakBindings as $key => $value) {
            $this->app->bindIf($key, $value);
        }
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath => config_path(basename($this->configPath)),
        ]);

        resolve(EngineManager::class)->extend('opensearch', static fn () => resolve(Engine::class));
    }
}
