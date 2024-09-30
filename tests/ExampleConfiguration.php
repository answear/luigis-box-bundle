<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests;

use Answear\LuigisBoxBundle\Service\ConfigProvider;

class ExampleConfiguration
{
    public static function provideDefaultConfig(): ConfigProvider
    {
        return new ConfigProvider(
            'config_name',
            [
                'config_name' => [
                    'host' => 'host',
                    'publicKey' => 'public-key',
                    'privateKey' => 'private-key',
                    'connectionTimeout' => 5.0,
                    'requestTimeout' => 5.0,
                    'searchTimeout' => 2.0,
                    'searchCacheTtl' => 300,
                ],
            ]
        );
    }
}
