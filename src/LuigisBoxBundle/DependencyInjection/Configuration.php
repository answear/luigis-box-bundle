<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const HOST = 'https://live.luigisbox.com';
    private const CONNECTION_TIMEOUT = 4.0;
    private const REQUEST_TIMEOUT = 8.0;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('answear_luigis_box');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->arrayNode('configs')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('host')->cannotBeEmpty()->defaultValue(self::HOST)->end()
                        ->scalarNode('publicKey')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('privateKey')->isRequired()->cannotBeEmpty()->end()
                        ->floatNode('connectionTimeout')->defaultValue(self::CONNECTION_TIMEOUT)->end()
                        ->floatNode('requestTimeout')->defaultValue(self::REQUEST_TIMEOUT)->end()
                    ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
