<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Factories;

use Illuminate\Support\Collection;

interface DocumentFactoryInterface
{
    public function makeFromModels(Collection $models): Collection;
}
