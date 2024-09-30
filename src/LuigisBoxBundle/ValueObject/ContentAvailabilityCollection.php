<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

readonly class ContentAvailabilityCollection implements \Countable
{
    /**
     * @param ContentAvailability[] $objects
     */
    public function __construct(public array $objects)
    {
        Assert::allIsInstanceOf($objects, ContentAvailability::class);
    }

    public function count(): int
    {
        return \count($this->objects);
    }
}
