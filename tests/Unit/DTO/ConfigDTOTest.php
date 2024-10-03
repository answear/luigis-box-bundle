<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\DependencyInjection;

use Answear\LuigisBoxBundle\DTO\ConfigDTO;
use Answear\LuigisBoxBundle\Tests\ExampleConfiguration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ConfigDTOTest extends TestCase
{
    #[Test]
    public function addConfig(): void
    {
        $configProvider = ExampleConfiguration::provideDefaultConfig();

        $config = new ConfigDTO('new-public', 'new-private');
        $configProvider->addConfig('new_config', $config);

        $this->assertEquals('public-key', $configProvider->getPublicKey());

        $configProvider->setConfig('new_config');

        $this->assertEquals('new-public', $configProvider->getPublicKey());
    }

    #[Test]
    public function addConfigWithColidingName(): void
    {
        $this->expectExceptionMessage('Configuration with key "config_name" already exists.');
        $configProvider = ExampleConfiguration::provideDefaultConfig();

        $config = new ConfigDTO('public', 'private');
        $configProvider->addConfig('config_name', $config);
    }
}
