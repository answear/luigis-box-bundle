<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

readonly class ContentUpdateCollection implements ObjectsInterface, \Countable
{
    /**
     * @param AbstractContentUpdate[] $objects
     */
    public function __construct(public array $objects)
    {
        Assert::allIsInstanceOf($objects, AbstractContentUpdate::class);
    }

    public static function fromContentAvailabilityObjects(ContentAvailabilityCollection $objects): self
    {
        $contentUpdateObjects = [];
        foreach ($objects->objects as $object) {
            $contentUpdateObjects[] = PartialContentUpdate::fromContentAvailability($object);
        }

        return new self($contentUpdateObjects);
    }

    public function count(): int
    {
        return \count($this->objects);
    }
}
