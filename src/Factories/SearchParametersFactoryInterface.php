<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Factories;

use Laravel\Scout\Builder;
use OpenSearch\Adapter\Search\SearchParameters;

interface SearchParametersFactoryInterface
{
    public function makeFromBuilder(Builder $builder, array $options = []): SearchParameters;
}
