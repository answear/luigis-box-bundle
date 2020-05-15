<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response;

use Answear\LuigisBoxBundle\ValueObject\SearchUrlBuilder;

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

    /**
     * @var string
     */
    private $passedUrl;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string|null
     */
    private $correctedQuery;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var Search\Hit[]
     */
    private $hits;

    /**
     * @var Search\Hit[]
     */
    private $quickSearchHits;

    /**
     * @var Search\Facet[]
     */
    private $facets;

    /**
     * @var int
     */
    private $totalHits;

    /**
     * @var int
     */
    private $currentSize;

    public function __construct(string $passedUrl, array $response)
    {
        $this->passedUrl = $passedUrl;

        $result = $response[self::RESULTS_PARAM];
        $this->query = $result[self::RESULTS_QUERY_PARAM];
        $this->correctedQuery = $result[self::RESULTS_CORRECTED_QUERY_PARAM] ?? null;

        $this->filters = $this->prepareFilters($result[self::RESULTS_FILTERS_PARAM]);

        $this->hits = $this->prepareHits($result[self::RESULTS_HITS_PARAM]);
        $this->quickSearchHits = $this->prepareHits($result[self::RESULTS_QUICKSEARCH_HITS_PARAM]);
        $this->facets = $this->prepareFacets($result[self::RESULTS_FACETS_PARAM]);

        $this->totalHits = (int) $result[self::RESULTS_TOTAL_HITS_PARAM];
        $this->currentSize = isset($result[self::RESULTS_OFFSET_PARAM]) ? (int) $result[self::RESULTS_OFFSET_PARAM] : $result[self::RESULTS_TOTAL_HITS_PARAM];
    }

    public function getPassedUrl(): string
    {
        return $this->passedUrl;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getCorrectedQuery(): ?string
    {
        return $this->correctedQuery;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return Search\Hit[]
     */
    public function getHits(): array
    {
        return $this->hits;
    }

    /**
     * @return Search\Hit[]
     */
    public function getQuickSearchHits(): array
    {
        return $this->quickSearchHits;
    }

    /**
     * @return Search\Facet[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    public function getTotalHits(): int
    {
        return $this->totalHits;
    }

    public function getCurrentSize(): int
    {
        return $this->currentSize;
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
