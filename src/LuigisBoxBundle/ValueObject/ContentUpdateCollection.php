<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentUpdateCollection implements ObjectsInterface, \Countable
{
    /**
     * @var AbstractContentUpdate[]
     */
    private $objects;

    public function __construct(array $objects)
    {
        Assert::allIsInstanceOf($objects, AbstractContentUpdate::class);

        $this->objects = $objects;
    }

    public static function fromContentAvailabilityObjects(ContentAvailabilityCollection $objects): self
    {
        $contentUpdateObjects = [];
        foreach ($objects->getObjects() as $object) {
            $contentUpdateObjects[] = PartialContentUpdate::fromContentAvailability($object);
        }

        return new self($contentUpdateObjects);
    }

    /**
     * @return AbstractContentUpdate[]
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
