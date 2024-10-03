<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

use Webmozart\Assert\Assert;

class PartialContentUpdate extends AbstractContentUpdate
{
    public static function fromContentAvailability(ContentAvailability $object): self
    {
        return new self(
            $object->url,
            null,
            [
                'availability' => $object->available ? 1 : 0,
            ]
        );
    }

    public function setNested(array $nested): void
    {
        Assert::allIsInstanceOf($nested, AbstractContentUpdate::class);
        $this->nested = $nested;
    }
}
