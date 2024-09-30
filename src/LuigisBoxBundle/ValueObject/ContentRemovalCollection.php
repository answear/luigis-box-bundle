<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

readonly class ContentRemovalCollection implements ObjectsInterface, \Countable
{
    public function __construct(public array $objects)
    {
        Assert::allIsInstanceOf($objects, ContentRemoval::class);
    }

    public function count(): int
    {
        return \count($this->objects);
    }
}
