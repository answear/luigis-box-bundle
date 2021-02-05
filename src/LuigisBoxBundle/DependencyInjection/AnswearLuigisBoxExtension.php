<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DependencyInjection;

use Answear\LuigisBoxBundle\Service\ConfigProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AnswearLuigisBoxExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if (empty($config['default_config']) && \count($config['configs']) > 1) {
            throw new \InvalidArgumentException(
                'Provide default_config name if more configs provided.'
            );
        }
        $config['default_config'] = $config['default_config'] ?? array_key_first($config['configs']);

        $definition = $container->getDefinition(ConfigProvider::class);
        $definition->setArguments(
            [
                $config['default_config'],
                $config['configs'],
            ]
        );
    }
}
