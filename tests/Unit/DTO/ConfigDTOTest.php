<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\DependencyInjection;

use Answear\LuigisBoxBundle\DTO\ConfigDTO;
use Answear\LuigisBoxBundle\Tests\DataProvider\Faker\ExampleConfiguration;
use PHPUnit\Framework\TestCase;

class ConfigDTOTest extends TestCase
{
    /**
     * @test
     */
    public function addConfig()
    {
        $configProvider = ExampleConfiguration::provideDefaultConfig();

        $config = new ConfigDTO('new-public', 'new-private');
        $configProvider->addConfig('new_config', $config);

        $this->assertEquals('public-key', $configProvider->getPublicKey());

        $configProvider->setConfig('new_config');

        $this->assertEquals('new-public', $configProvider->getPublicKey());
    }

    /**
     * @test
     */
    public function addConfigWithColidingName()
    {
        $this->expectExceptionMessage('Configuration with key "config_name" already exists.');
        $configProvider = ExampleConfiguration::provideDefaultConfig();

        $config = new ConfigDTO('public', 'private');
        $configProvider->addConfig('config_name', $config);
    }
}
