<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\ValueObject;

readonly class Recommendation
{
    public function __construct(
        public string $recommendationType,
        public ?string $userId = null,
        public ?array $hitFields = null,
        public ?array $itemIds = null,
        public ?array $blacklistedItemIds = null,
        public ?int $size = null,
        public ?array $recommendationContext = null,
        public ?array $settingsOverride = null,
        public ?bool $markFallbackResults = null,
        public ?string $recommenderClientIdentifier = null,
    ) {
    }
}
