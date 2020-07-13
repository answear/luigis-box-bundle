<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\Search;

use Webmozart\Assert\Assert;

class Facet
{
    private const NAME_PARAM = 'name';
    private const TYPE_PARAM = 'type';
    private const VALUES_PARAM = 'values';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var FacetValue[]
     */
    private $values = [];

    public function __construct(array $facetData)
    {
        Assert::string($facetData[self::NAME_PARAM]);
        Assert::string($facetData[self::TYPE_PARAM]);
        Assert::isArray($facetData[self::VALUES_PARAM]);

        $this->name = $facetData[self::NAME_PARAM];
        $this->type = $facetData[self::TYPE_PARAM];
        foreach ($facetData[self::VALUES_PARAM] as $facetValueData) {
            $this->values[] = new FacetValue($facetValueData);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return FacetValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
