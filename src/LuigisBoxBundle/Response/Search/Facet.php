<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\Search;

use Webmozart\Assert\Assert;

class Facet
{
    private const NAME_PARAM = 'name';
    private const TYPE_PARAM = 'type';
    private const VALUES_PARAM = 'values';

    public readonly string $name;

    public readonly string $type;

    /**
     * @var FacetValue[]
     */
    public readonly array $values;

    public function __construct(array $facetData)
    {
        Assert::string($facetData[self::NAME_PARAM]);
        Assert::string($facetData[self::TYPE_PARAM]);
        Assert::isArray($facetData[self::VALUES_PARAM]);

        $this->name = $facetData[self::NAME_PARAM];
        $this->type = $facetData[self::TYPE_PARAM];

        $values = [];
        foreach ($facetData[self::VALUES_PARAM] as $facetValueData) {
            $values[] = new FacetValue($facetValueData);
        }
        $this->values = $values;
    }
}
