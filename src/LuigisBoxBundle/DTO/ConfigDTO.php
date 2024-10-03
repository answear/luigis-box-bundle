<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DTO;

use Answear\LuigisBoxBundle\DependencyInjection\Configuration;
use Webmozart\Assert\Assert;

readonly class ConfigDTO
{
    private const MAX_SEARCH_CACHE_TTL = 300;

    public int $searchCacheTtl;

    public function __construct(
        public string $publicKey,
        public string $privateKey,
        public string $host = Configuration::HOST,
        public float $connectionTimeout = Configuration::CONNECTION_TIMEOUT,
        public float $requestTimeout = Configuration::REQUEST_TIMEOUT,
        public float $searchTimeout = Configuration::SEARCH_TIMEOUT,
        int $searchCacheTtl = Configuration::SEARCH_CACHE_TIMEOUT,
    ) {
        Assert::greaterThanEq($searchCacheTtl, 0, 'searchCacheTtl cannot be negative.');
        $this->searchCacheTtl = min($searchCacheTtl, self::MAX_SEARCH_CACHE_TTL);
    }
}
