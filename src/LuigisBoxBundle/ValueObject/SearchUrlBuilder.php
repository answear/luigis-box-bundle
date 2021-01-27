<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

/**
 * @see https://live.luigisbox.com/#search-as-a-service-tips
 */
class SearchUrlBuilder
{
    public const ARRAY_ITEM_SEPARATOR = ':';
    public const LIST_SEPARATOR = ',';
    public const DEFAULT_SIZE = 10;
    public const RANGE_SEPARATOR = '|';
    private const AVAILABLE_ORDER_DIRECTIONS = ['asc', 'desc'];

    /**
     * @var string|null
     */
    private $query;

    /**
     * @var bool|null
     */
    private $queryUnderstanding;

    /**
     * @var array|null
     */
    private $filters;

    /**
     * @var array|null
     */
    private $mustFilters;

    /**
     * @var int
     */
    private $size = self::DEFAULT_SIZE;

    /**
     * @var string|null
     */
    private $sort;

    /**
     * @var string|null
     */
    private $quicksearchTypes;

    /**
     * @var string|null
     */
    private $facets;

    /**
     * @var int
     */
    private $page;

    /**
     * @var bool|null
     */
    private $useFixits;

    /**
     * @var array|null
     */
    private $prefer;

    /**
     * @var string|null
     */
    private $hitFields;

    /**
     * @var Search\Context|null
     */
    private $context;

    /**
     * @var string|null
     */
    private $userId;

    public function __construct(int $page = 1)
    {
        $this->page = $page;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function enableQueryUnderstanding(): self
    {
        if (null === $this->userId) {
            throw new \InvalidArgumentException('Set $userId first.');
        }

        $this->queryUnderstanding = true;

        return $this;
    }

    /**
     * @param bool|int|string $value
     */
    public function addFilter(string $key, $value): self
    {
        if (!is_bool($value)
            && !is_int($value)
            && !is_string($value)) {
            throw new \InvalidArgumentException(
                sprintf('Value must be bool|int|string, %s provided.', gettype($value))
            );
        }

        $this->filters[$key] = $this->filters[$key] ?? [];
        if (\is_string($this->filters[$key])) {
            $this->filters[$key] = [$this->filters[$key]];
        }

        if (is_bool($value)) {
            $this->filters[$key][] = false === $value ? 'false' : 'true';
        } else {
            $this->filters[$key][] = $value;
        }

        $this->filters[$key] = array_unique($this->filters[$key]);

        return $this;
    }

    public function setFilters(array $filters): self
    {
        if (\count($this->filters ?? []) > 0) {
            throw new \LogicException(
                'You already have the filters set. Use resetFilters() method to clear them first.'
            );
        }

        $keys = array_keys($filters);
        Assert::allString($keys, 'All filters keys must be string.');

        $this->filters = $filters;

        return $this;
    }

    /**
     * @param bool|int|string $value
     */
    public function addMustFilter(string $key, $value): self
    {
        if (!is_bool($value)
            && !is_int($value)
            && !is_string($value)) {
            throw new \InvalidArgumentException(
                sprintf('Value must be bool|int|string, %s provided.', gettype($value))
            );
        }

        $this->mustFilters[$key] = $this->mustFilters[$key] ?? [];
        if (\is_string($this->mustFilters[$key])) {
            $this->mustFilters[$key] = [$this->mustFilters[$key]];
        }

        if (is_bool($value)) {
            $this->mustFilters[$key][] = false === $value ? 'false' : 'true';
        } else {
            $this->mustFilters[$key][] = $value;
        }

        $this->mustFilters[$key] = array_unique($this->mustFilters[$key]);

        return $this;
    }

    public function setMustFilters(array $filters): self
    {
        if (\count($this->mustFilters ?? []) > 0) {
            throw new \LogicException(
                'You already have the must filters set. Use resetMustFilters() method to clear them first.'
            );
        }

        $keys = array_keys($filters);
        Assert::allString($keys, 'All must filters keys must be string.');

        $this->mustFilters = $filters;

        return $this;
    }

    public function resetFilters(): self
    {
        $this->filters = null;

        return $this;
    }

    public function resetMustFilters(): self
    {
        $this->mustFilters = null;

        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function setSort(string $sort, string $direction): self
    {
        Assert::oneOf(
            $direction,
            self::AVAILABLE_ORDER_DIRECTIONS,
            sprintf('The sort direction parameter must by one of %s.', implode(',', self::AVAILABLE_ORDER_DIRECTIONS))
        );

        $this->sort = $sort . self::ARRAY_ITEM_SEPARATOR . $direction;

        return $this;
    }

    public function setQuicksearchTypes(array $quicksearchTypes): self
    {
        Assert::allString($quicksearchTypes);

        $this->quicksearchTypes = implode(self::LIST_SEPARATOR, $quicksearchTypes);

        return $this;
    }

    public function setFacets(array $facets): self
    {
        $this->facets = implode(self::LIST_SEPARATOR, $facets);

        return $this;
    }

    public function setFixits(bool $fixits): self
    {
        $this->useFixits = $fixits;

        return $this;
    }

    public function addPrefer(string $key, string $value): self
    {
        $this->prefer[$key] = $this->prefer[$key] ?? [];
        if (\is_string($this->prefer[$key])) {
            $this->prefer[$key] = [$this->prefer[$key]];
        }
        $this->prefer[$key][] = $value;

        $this->prefer[$key] = array_unique($this->prefer[$key]);

        return $this;
    }

    public function setPreferArray(array $preferArray): self
    {
        $this->prefer = $preferArray;

        return $this;
    }

    public function setHitFields(array $fields): self
    {
        Assert::allString($fields);

        $this->hitFields = implode(self::LIST_SEPARATOR, $fields);

        return $this;
    }

    public function setContext(Search\Context $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function toUrlQuery(): string
    {
        $queryFields = [
            'size' => $this->size,
            'page' => $this->page,
        ];

        if (null !== $this->query) {
            $queryFields['q'] = $this->query;
        }

        if (null !== $this->queryUnderstanding) {
            $queryFields['qu'] = true === $this->queryUnderstanding ? 1 : 0;
        }

        if (null !== $this->userId) {
            $queryFields['user_id'] = $this->userId;
        }

        if (null !== $this->filters) {
            $filtersFields = [];
            foreach ($this->filters as $key => $values) {
                if (\is_array($values)) {
                    foreach ($values as $value) {
                        $filtersFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $value;
                    }
                } else {
                    $filtersFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $values;
                }
            }
            $queryFields['f'] = $filtersFields;
        }

        if (null !== $this->mustFilters) {
            $filtersFields = [];
            foreach ($this->mustFilters as $key => $values) {
                if (\is_array($values)) {
                    foreach ($values as $value) {
                        $filtersFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $value;
                    }
                } else {
                    $filtersFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $values;
                }
            }
            $queryFields['f_must'] = $filtersFields;
        }

        if (null !== $this->sort) {
            $queryFields['sort'] = $this->sort;
        }

        if (null !== $this->quicksearchTypes) {
            $queryFields['quicksearch_types'] = $this->quicksearchTypes;
        }

        if (null !== $this->facets) {
            $queryFields['facets'] = $this->facets;
        }

        if (null !== $this->useFixits) {
            $queryFields['use_fixits'] = true === $this->useFixits ? 1 : 0;
        }

        if (null !== $this->prefer) {
            $preferFields = [];
            foreach ($this->prefer as $key => $values) {
                if (\is_array($values)) {
                    foreach ($values as $value) {
                        $preferFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $value;
                    }
                } else {
                    $preferFields[] = $key . self::ARRAY_ITEM_SEPARATOR . $values;
                }
            }
            $queryFields['prefer'] = $preferFields;
        }

        if (null !== $this->hitFields) {
            $queryFields['hit_fields'] = $this->hitFields;
        }

        if (null !== $this->context) {
            $context = [];
            if (null !== $this->context->getGeoLocation()) {
                $context['geo_location'] = $this->context->getGeoLocation();
            }
            if (null !== $this->context->getGeoLocationField()) {
                $context['geo_location_field'] = $this->context->getGeoLocationField();
            }
            if (null !== $this->context->getAvailabilityField()) {
                $context['availability_field'] = $this->context->getAvailabilityField();
            }
            if (null !== $this->context->getBoostField()) {
                $context['boost_field'] = $this->context->getBoostField();
            }
            if (null !== $this->context->getFreshnessField()) {
                $context['freshness_field'] = $this->context->getFreshnessField();
            }
            if (!empty($context)) {
                $queryFields['context'] = $context;
            }
        }

        return preg_replace('/%5B(\d+)%5D=/', '%5B%5D=', http_build_query($queryFields));
    }

    public function __toString(): string
    {
        return $this->toUrlQuery();
    }
}
