<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\Util\AuthenticationUtil;

class ConfigProvider
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $publicKey;

    /**
     * @var string
     */
    public $privateKey;

    /**
     * @var float
     */
    public $connectionTimeout;

    /**
     * @var float
     */
    public $requestTimeout;

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

    public function getRequestHeaders(string $httpMethod, string $endpoint, \DateTimeInterface $date): array
    {
        return AuthenticationUtil::getRequestHeaders(
            $this->publicKey,
            $this->privateKey,
            $httpMethod,
            $endpoint,
            $date
        );
    }
}
