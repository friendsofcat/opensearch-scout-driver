<?php declare(strict_types=1);

namespace OpenSearch\ScoutDriver\Factories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;
use Laravel\Scout\Builder;
use OpenSearch\Adapter\Search\SearchResult;

interface ModelFactoryInterface
{
    public function makeFromSearchResult(SearchResult $searchResult, Builder $builder): Collection;

    public function makeLazyFromSearchResult(SearchResult $searchResult, Builder $builder): LazyCollection;
}
