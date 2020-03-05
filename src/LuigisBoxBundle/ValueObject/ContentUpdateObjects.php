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

    public static function fromContentAvailabilityObjects(ContentAvailabilityObjects $objects): self
    {
        $contentUpdateObjects = [];
        foreach ($objects->getObjects() as $object) {
            $contentUpdateObjects[] = ContentUpdate::fromContentAvailability($object);
        }

        return new self($contentUpdateObjects);
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
