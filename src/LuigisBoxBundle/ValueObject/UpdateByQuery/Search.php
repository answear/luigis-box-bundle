<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

use Webmozart\Assert\Assert;

class Search
{
    /**
     * @var string[]
     */
    private $types;

    /**
     * @var array
     */
    private $partial = ['fields' => []];

    public function __construct(array $types, array $fields)
    {
        Assert::allString($types);
        Assert::notEmpty($fields);

        $this->types = $types;
        $this->partial['fields'] = $fields;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getPartial(): array
    {
        return $this->partial;
    }
}
