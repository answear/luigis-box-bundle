<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentUpdateObjects implements ObjectsInterface, \Countable
{
    /**
     * @var ContentUpdate[]
     */
    private $objects;

    public function __construct(array $objects)
    {
        Assert::allIsInstanceOf($objects, ContentUpdate::class);

        $this->objects = $objects;
    }

    /**
     * @return ContentUpdate[]
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
