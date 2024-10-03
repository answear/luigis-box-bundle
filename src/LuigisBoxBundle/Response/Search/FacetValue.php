<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\Search;

use Webmozart\Assert\Assert;

class FacetValue
{
    private const VALUE_PARAM = 'value';
    private const HITS_COUNT_PARAM = 'hits_count';

    public readonly string $value;

    public readonly int $hitsCount;

    public function __construct(array $facetValueData)
    {
        Assert::string($facetValueData[self::VALUE_PARAM]);
        Assert::integer($facetValueData[self::HITS_COUNT_PARAM]);

        $this->value = $facetValueData[self::VALUE_PARAM];
        $this->hitsCount = $facetValueData[self::HITS_COUNT_PARAM];
    }
}
