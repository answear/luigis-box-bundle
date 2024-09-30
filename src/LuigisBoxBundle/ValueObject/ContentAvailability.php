<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

readonly class ContentAvailability
{
    public function __construct(
        public string $url,
        public bool $available,
    ) {
    }
}
