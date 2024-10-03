<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

readonly class ContentRemoval
{
    public function __construct(
        public string $url,
        public string $type,
    ) {
    }
}
