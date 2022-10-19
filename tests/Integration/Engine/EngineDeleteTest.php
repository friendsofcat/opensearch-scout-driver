<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Tests\Integration\Engine;

use Illuminate\Database\Eloquent\Model;
use OpenSearch\Adapter\Documents\DocumentManager;
use OpenSearch\Adapter\Indices\IndexManager;
use OpenSearch\ScoutDriver\Engine;
use OpenSearch\ScoutDriver\Factories\DocumentFactoryInterface;
use OpenSearch\ScoutDriver\Factories\ModelFactoryInterface;
use OpenSearch\ScoutDriver\Factories\SearchParametersFactoryInterface;
use OpenSearch\ScoutDriver\Tests\App\Client;
use OpenSearch\ScoutDriver\Tests\Integration\TestCase;

/**
 * @covers \OpenSearch\ScoutDriver\Engine
 *
 * @uses   \OpenSearch\ScoutDriver\Factories\DocumentFactory
 * @uses   \OpenSearch\ScoutDriver\Factories\ModelFactory
 * @uses   \OpenSearch\ScoutDriver\Factories\SearchParametersFactory
 */
final class EngineDeleteTest extends TestCase
{
    public function test_empty_model_collection_can_not_be_deleted_from_index(): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->never())->method('delete');

        $engine = new Engine(
            $documentManager,
            resolve(DocumentFactoryInterface::class),
            resolve(SearchParametersFactoryInterface::class),
            resolve(ModelFactoryInterface::class),
            resolve(IndexManager::class)
        );

        $engine->delete((new Client())->newCollection());
    }

    public function test_not_empty_model_collection_can_be_deleted_from_index(): void
    {
        $source = factory(Client::class, rand(6, 10))->create();

        $deleted = $source->slice(0, rand(2, 4))->each(static function (Client $client) {
            $client->forceDelete();
        });

        $found = Client::search()->get();

        // assert that found fewer documents than in the source
        $this->assertSame($source->count() - $deleted->count(), $found->count());
        // assert that deleted models are not found
        $this->assertCount(0, $deleted->pluck('id')->intersect($found->pluck('id')));
    }

    public function test_not_found_error_is_ignored_when_models_are_being_deleted_from_index(): void
    {
        $clients = factory(Client::class, rand(2, 10))->create();

        // remove models from index
        $clients->unsearchable();

        $clients->each(function (Client $client) {
            $client->forceDelete();

            $this->assertDatabaseMissing(
                $client->getTable(),
                [$client->getKeyName() => $client->getKey()]
            );
        });
    }

    public function test_models_can_be_flushed_from_index(): void
    {
        factory(Client::class, rand(2, 10))->create();

        Client::removeAllFromSearch();

        $found = Client::search()->get();

        // assert that nothing is found
        $this->assertSame(0, $found->count());
    }

    public function test_models_can_be_soft_deleted_from_index(): void
    {
        // enable soft deletes
        $this->config->set('scout.soft_delete', true);

        $source = factory(Client::class, rand(2, 10))->create();

        $source->each(static function (Model $client) {
            $client->delete();
        });

        $found = Client::search()->withTrashed()->get();

        $found->each(function (Model $client) {
            $this->assertNotNull($client->deleted_at);
        });
    }
}
