<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Tests\Integration\Engine;

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
final class EngineUpdateTest extends TestCase
{
    public function test_empty_model_collection_can_not_be_indexed(): void
    {
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager->expects($this->never())->method('index');

        $engine = new Engine(
            $documentManager,
            resolve(DocumentFactoryInterface::class),
            resolve(SearchParametersFactoryInterface::class),
            resolve(ModelFactoryInterface::class),
            resolve(IndexManager::class)
        );

        $engine->update((new Client())->newCollection());
    }

    public function test_not_empty_model_collection_can_be_indexed(): void
    {
        $source = factory(Client::class, rand(2, 10))->create();
        $found = Client::search()->get();

        // assert that the amount of created models corresponds number of found models
        $this->assertSame($source->count(), $found->count());
        // assert that all source models are found
        $this->assertCount(0, $source->pluck('id')->diff($found->pluck('id')));
    }
}
