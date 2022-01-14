<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\Tests\Acceptance\DependencyInjection;

use Answear\LuigisBoxBundle\DependencyInjection\AnswearLuigisBoxExtension;
use Answear\LuigisBoxBundle\DependencyInjection\Configuration;
use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     * @dataProvider provideValidConfig
     */
    public function validTest(array $configs, string $expectedConfigName): void
    {
        $extension = $this->getExtension();

        $builder = new ContainerBuilder();
        $extension->load($configs, $builder);

        $configProviderDefinition = $builder->getDefinition(ConfigProvider::class);

        self::assertSame($expectedConfigName, $configProviderDefinition->getArgument(0));
        self::assertIsArray($configProviderDefinition->getArgument(1));
    }

    /**
     * @test
     * @dataProvider provideInvalidConfig
     */
    public function invalid(array $config, ?string $expectedMessage = null): void
    {
        $this->assertConfigurationIsInvalid(
            $config,
            $expectedMessage
        );
    }

    /**
     * @test
     * @dataProvider provideValidConfig
     */
    public function valid(array $config): void
    {
        $this->assertConfigurationIsValid($config);
    }

    public function provideInvalidConfig(): iterable
    {
        yield [
            [
                [],
            ],
            '"configs"',
        ];

        yield [
            [
                [
                    'configs' => [],
                ],
            ],
            'The path "answear_luigis_box.configs" should have at least 1 element(s) defined.',
        ];

        yield [
            [
                [
                    'configs' => [
                        'config_name' => [],
                    ],
                ],
            ],
            '"publicKey"',
        ];

        yield [
            [
                [
                    'configs' => [
                        'config_name' => [
                            'publicKey' => '',
                        ],
                    ],
                ],
            ],
            'The path "answear_luigis_box.configs.config_name.publicKey" cannot contain an empty value, but got "".',
        ];

        yield [
            [
                [
                    'configs' => [
                        'config_name' => [
                            'host' => '',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'The path "answear_luigis_box.configs.config_name.host" cannot contain an empty value, but got "".',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                            'searchCacheTtl' => '8',
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'Invalid type for path "answear_luigis_box.configs.config_name.searchCacheTtl". Expected ',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                            'searchCacheTtl' => 8.5,
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'Invalid type for path "answear_luigis_box.configs.config_name.searchCacheTtl". Expected ',
        ];
    }

    public function provideValidConfig(): iterable
    {
        yield [
            [
                [
                    'configs' => [
                        'config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'config_name',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'other_config_name',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'other_config_name',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                            'searchCacheTtl' => 0,
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'other_config_name',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                            'searchCacheTtl' => 100,
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'other_config_name',
        ];

        yield [
            [
                [
                    'default_config' => 'other_config_name',
                    'configs' => [
                        'config_name' => [
                            'host' => 'host',
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                            'connectionTimeout' => 23.2,
                            'requestTimeout' => 17,
                            'searchTimeout' => 4,
                            'searchCacheTtl' => 500,
                        ],
                        'other_config_name' => [
                            'publicKey' => 'public',
                            'privateKey' => 'private',
                        ],
                    ],
                ],
            ],
            'other_config_name',
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [$this->getExtension()];
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    private function getExtension(): AnswearLuigisBoxExtension
    {
        return new AnswearLuigisBoxExtension();
    }
}
