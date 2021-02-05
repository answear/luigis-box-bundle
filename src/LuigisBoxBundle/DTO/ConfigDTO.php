<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DTO;

class ConfigDTO
{
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

    public function __construct(
        string $host,
        string $publicKey,
        string $privateKey,
        float $connectionTimeout,
        float $requestTimeout
    ) {
        $this->host = $host;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->connectionTimeout = $connectionTimeout;
        $this->requestTimeout = $requestTimeout;
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
}
