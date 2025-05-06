<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Service;

use Answear\LuigisBoxBundle\DTO\ConfigDTO;
use Answear\LuigisBoxBundle\Util\AuthenticationUtil;
use Webmozart\Assert\Assert;

class ConfigProvider
{
    public const API_VERSION = 'v1';

    private string $configName;

    /**
     * @var string[]
     */
    public array $headers = [];

    /**
     * @var ConfigDTO[]
     */
    private array $configs;

    public function __construct(
        string $defaultConfigName,
        array $configs,
    ) {
        $configsDTO = [];
        foreach ($configs as $configName => $item) {
            Assert::keyExists($item, 'host');
            Assert::keyExists($item, 'publicKey');
            Assert::keyExists($item, 'privateKey');
            Assert::keyExists($item, 'connectionTimeout');
            Assert::keyExists($item, 'requestTimeout');
            Assert::keyExists($item, 'searchTimeout');
            Assert::keyExists($item, 'searchCacheTtl');
            Assert::keyExists($item, 'recommendationsRequestTimeout');
            Assert::keyExists($item, 'recommendationsConnectionTimeout');

            $configsDTO[$configName] = new ConfigDTO(
                $item['publicKey'],
                $item['privateKey'],
                rtrim($item['host'], '/'),
                $item['connectionTimeout'],
                $item['requestTimeout'],
                $item['searchTimeout'],
                $item['searchCacheTtl'],
                $item['recommendationsRequestTimeout'],
                $item['recommendationsConnectionTimeout'],
            );
        }

        Assert::allIsInstanceOf($configsDTO, ConfigDTO::class);
        Assert::keyExists(
            $configsDTO,
            $defaultConfigName,
            sprintf(
                'No configuration with key "%s". Available configurations: %s.',
                $defaultConfigName,
                implode(', ', array_keys($configsDTO))
            )
        );

        $this->configName = $defaultConfigName;
        $this->configs = $configsDTO;
    }

    public function addConfig(string $configName, ConfigDTO $configDTO): void
    {
        Assert::keyNotExists(
            $this->configs,
            $configName,
            sprintf(
                'Configuration with key "%s" already exists.',
                $configName,
            )
        );

        $this->configs[$configName] = $configDTO;
    }

    public function setConfig(string $configName): void
    {
        Assert::keyExists(
            $this->configs,
            $configName,
            sprintf(
                'No configuration with key "%s". Available configurations: %s.',
                $configName,
                implode(', ', array_keys($this->configs))
            )
        );
        $this->configName = $configName;
    }

    public function setHeader(string $name, string $value): void
    {
        $name = trim($name);
        if (in_array(
            $name,
            [
                AuthenticationUtil::HEADER_CONTENT_TYPE,
                AuthenticationUtil::HEADER_DATE,
                AuthenticationUtil::HEADER_AUTHORIZATION,
            ],
            true
        )) {
            throw new \BadMethodCallException('Reserved header name provided: ' . $name);
        }

        $this->headers[$name] = $value;
    }

    public function resetHeaders(): void
    {
        $this->headers = [];
    }

    public function getAuthorizationHeaders(string $httpMethod, string $endpoint, \DateTimeInterface $date): array
    {
        $configDTO = $this->getConfigDTO();

        return AuthenticationUtil::getRequestHeaders(
            $configDTO->publicKey,
            $configDTO->privateKey,
            $httpMethod,
            $endpoint,
            $date
        );
    }

    public function getHost(): string
    {
        return $this->getConfigDTO()->host;
    }

    public function getPublicKey(): string
    {
        return $this->getConfigDTO()->publicKey;
    }

    public function getConnectionTimeout(): float
    {
        return $this->getConfigDTO()->connectionTimeout;
    }

    public function getRequestTimeout(): float
    {
        return $this->getConfigDTO()->requestTimeout;
    }

    public function getSearchTimeout(): float
    {
        return $this->getConfigDTO()->searchTimeout;
    }

    public function getSearchCacheTtl(): int
    {
        return $this->getConfigDTO()->searchCacheTtl;
    }

    public function getRecommendationsRequestTimeout(): float
    {
        return $this->getConfigDTO()->recommendationsRequestTimeout;
    }

    public function getRecommendationsConnectionTimeout(): float
    {
        return $this->getConfigDTO()->recommendationsConnectionTimeout;
    }

    private function getConfigDTO(): ConfigDTO
    {
        return $this->configs[$this->configName];
    }
}
