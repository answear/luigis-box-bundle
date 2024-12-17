<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

readonly class RecommendationsCollection implements ObjectsInterface, ArrayWrapInterface, \Countable
{
    /**
     * @param Recommendation[] $objects
     */
    public function __construct(private array $objects)
    {
        Assert::allIsInstanceOf($objects, Recommendation::class);
    }

    public function count(): int
    {
        return \count($this->objects);
    }

    public function getObjects(): array
    {
        return $this->objects;
    }
}
