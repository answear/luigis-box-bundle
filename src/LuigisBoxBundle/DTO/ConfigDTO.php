<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DTO;

class ConfigDTO
{
    private const MAX_SEARCH_CACHE_TTL = 300;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var float
     */
    private $connectionTimeout;

    /**
     * @var float
     */
    private $requestTimeout;

    /**
     * @var float
     */
    private $searchTimeout;

    /**
     * @var int
     */
    private $searchCacheTtl;

    public function __construct(
        string $host,
        string $publicKey,
        string $privateKey,
        float $connectionTimeout,
        float $requestTimeout,
        float $searchTimeout,
        int $searchCacheTtl
    ) {
        if ($searchCacheTtl < 0) {
            throw new \InvalidArgumentException('searchCacheTtl cannot be negative.');
        }

        $this->host = $host;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->connectionTimeout = $connectionTimeout;
        $this->requestTimeout = $requestTimeout;
        $this->searchTimeout = $searchTimeout;
        $this->searchCacheTtl = min($searchCacheTtl, self::MAX_SEARCH_CACHE_TTL);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getConnectionTimeout(): float
    {
        return $this->connectionTimeout;
    }

    public function getRequestTimeout(): float
    {
        return $this->requestTimeout;
    }

    public function getSearchTimeout(): float
    {
        return $this->searchTimeout;
    }

    public function getSearchCacheTtl(): int
    {
        return $this->searchCacheTtl;
    }
}
