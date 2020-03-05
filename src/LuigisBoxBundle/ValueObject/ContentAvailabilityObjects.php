<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class ContentAvailabilityObjects
{
    /**
     * @var ContentAvailability[]
     */
    private $objects;

    public function __construct(array $objects)
    {
        Assert::allIsInstanceOf($objects, ContentAvailability::class);

        $this->objects = $objects;
    }

    /**
     * @return ContentAvailability[]
     */
    public function getObjects(): array
    {
        return $this->objects;
    }
}
