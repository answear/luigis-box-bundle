<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentRemovalCollection implements ObjectsInterface, \Countable
{
    /**
     * @var ContentRemoval[]
     */
    private $objects;

    public function __construct(array $objects)
    {
        Assert::allIsInstanceOf($objects, ContentRemoval::class);

        $this->objects = $objects;
    }

    /**
     * @return ContentRemoval[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    public function count(): int
    {
        return \count($this->getObjects());
    }
}
