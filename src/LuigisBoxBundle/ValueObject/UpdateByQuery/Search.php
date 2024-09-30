<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

use Webmozart\Assert\Assert;

readonly class Search
{
    public array $partial;

    /**
     * @param string[] $types
     */
    public function __construct(
        public array $types,
        array $fields = [],
    ) {
        Assert::allString($types);
        Assert::notEmpty($fields);

        $partial['fields'] = $fields;
        $this->partial = $partial;
    }
}
