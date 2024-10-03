<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject\UpdateByQuery;

use Webmozart\Assert\Assert;

readonly class Update
{
    public function __construct(public array $fields)
    {
        Assert::notEmpty($fields);
    }
}
