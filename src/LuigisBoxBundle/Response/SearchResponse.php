<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;
use Webmozart\Assert\Assert;

class SearchResponse
{
    private const RESULTS_OFFSET_PARAM = 'offset';
    private const RESULTS_TOTAL_HITS_PARAM = 'total_hits';
    private const RESULTS_FACETS_PARAM = 'facets';
    private const RESULTS_QUICKSEARCH_HITS_PARAM = 'quicksearch_hits';
    private const RESULTS_HITS_PARAM = 'hits';
    private const RESULTS_FILTERS_PARAM = 'filters';
    private const RESULTS_CORRECTED_QUERY_PARAM = 'corrected_query';
    private const RESULTS_QUERY_PARAM = 'query';
    private const RESULTS_PARAM = 'results';

    public readonly ?string $query;

    public readonly ?string $correctedQuery;

    public readonly array $filters;

    /**
     * @var Search\Hit[]
     */
    public readonly array $hits;

    /**
     * @var Search\Hit[]
     */
    public readonly array $quickSearchHits;

    /**
     * @var Search\Facet[]
     */
    public readonly array $facets;

    public readonly int $totalHits;

    public readonly int $currentSize;

    public function __construct(
        public readonly string $searchUrl,
        array $response,
    ) {
        $result = $response[self::RESULTS_PARAM];
        $query = $result[self::RESULTS_QUERY_PARAM] ?? null;
        $correctedQuery = $result[self::RESULTS_CORRECTED_QUERY_PARAM] ?? null;

        Assert::numeric($result[self::RESULTS_TOTAL_HITS_PARAM]);
        $totalHits = (int) $result[self::RESULTS_TOTAL_HITS_PARAM];

        Assert::isArray($result);
        Assert::nullOrString($query);
        Assert::nullOrString($correctedQuery);

        $this->query = $query;
        $this->correctedQuery = $correctedQuery;
        $this->filters = $this->prepareFilters($result[self::RESULTS_FILTERS_PARAM]);
        $this->hits = $this->prepareHits($result[self::RESULTS_HITS_PARAM]);
        $this->quickSearchHits = $this->prepareHits($result[self::RESULTS_QUICKSEARCH_HITS_PARAM]);
        $this->facets = $this->prepareFacets($result[self::RESULTS_FACETS_PARAM]);
        $this->totalHits = $totalHits;
        $this->currentSize = isset($result[self::RESULTS_OFFSET_PARAM]) ? (int) $result[self::RESULTS_OFFSET_PARAM] : $totalHits;
    }

    private function prepareFilters(array $filtersArray): array
    {
        $filters = [];
        foreach ($filtersArray as $stringFilter) {
            [$key, $value] = explode(SearchUrlBuilder::ARRAY_ITEM_SEPARATOR, $stringFilter);

            if (isset($filters[$key]) && !\is_array($filters[$key])) {
                $filters[$key] = [$filters[$key]];

                $filters[$key][] = $value;
            } else {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    /**
     * @return Search\Hit[]
     */
    private function prepareHits(array $hitsArray): array
    {
        $hits = [];
        foreach ($hitsArray as $hitArray) {
            $hits[] = new Search\Hit($hitArray);
        }

        return $hits;
    }

    private function prepareFacets(array $facetsArray): array
    {
        $facets = [];
        foreach ($facetsArray as $facetArray) {
            $facets[] = new Search\Facet($facetArray);
        }

        return $facets;
    }
}
