<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Response\Search;

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
     * @var array
     */
    private $values;

    public function __construct(array $facetData)
    {
        $this->name = $facetData[self::NAME_PARAM];
        $this->type = $facetData[self::TYPE_PARAM];
        $this->values = $facetData[self::VALUES_PARAM];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
