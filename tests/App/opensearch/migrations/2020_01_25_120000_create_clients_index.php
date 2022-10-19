<?php declare(strict_types=1);

use OpenSearch\Adapter\Indices\Mapping;
use OpenSearch\Adapter\Indices\Settings;
use OpenSearch\Migrations\Facades\Index;
use OpenSearch\Migrations\MigrationInterface;

final class CreateClientsIndex implements MigrationInterface
{
    public function up(): void
    {
        Index::create('clients', static function (Mapping $mapping, Settings $settings) {
            $mapping->text('name');
            $mapping->text('last_name');
            $mapping->keyword('phone_number');
            $mapping->keyword('email');
        });
    }

    public function down(): void
    {
        Index::dropIfExists('clients');
    }
}
