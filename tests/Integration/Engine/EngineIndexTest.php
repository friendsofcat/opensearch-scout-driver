<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Tests\Integration\Engine;

use InvalidArgumentException;
use OpenSearch\Adapter\Indices\IndexManager;
use OpenSearch\ScoutDriver\Engine;
use OpenSearch\ScoutDriver\Tests\Integration\TestCase;

/**
 * @covers \OpenSearch\ScoutDriver\Engine
 */
final class EngineIndexTest extends TestCase
{
    private const INDEX_NAME = 'opensearch-scout-driver-test-index';

    private IndexManager $indexManager;
    private Engine $engine;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indexManager = resolve(IndexManager::class);
        $this->engine = resolve(Engine::class);
    }

    public function test_index_with_given_name_can_be_created(): void
    {
        if ($this->indexManager->exists(self::INDEX_NAME)) {
            $this->engine->deleteIndex(self::INDEX_NAME);
        }

        $this->engine->createIndex(self::INDEX_NAME);
        $this->assertTrue($this->indexManager->exists(self::INDEX_NAME));
    }

    public function test_index_with_alternative_primary_key_can_not_be_created(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->engine->createIndex(self::INDEX_NAME, ['primaryKey' => 'bar']);
    }

    /**
     * @depends test_index_with_given_name_can_be_created
     */
    public function test_index_can_be_deleted_by_name(): void
    {
        $this->engine->deleteIndex(self::INDEX_NAME);
        $this->assertFalse($this->indexManager->exists(self::INDEX_NAME));
    }
}
