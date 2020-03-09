<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const HOST = 'https://live.luigisbox.com';
    private const CONNECTION_TIMEOUT = 5.0;
    private const REQUEST_TIMEOUT = 5.0;

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('answear_luigis_box');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('host')->defaultValue(self::HOST)->end()
                ->scalarNode('publicKey')->cannotBeEmpty()->end()
                ->scalarNode('privateKey')->cannotBeEmpty()->end()
                ->floatNode('connectionTimeout')->defaultValue(self::CONNECTION_TIMEOUT)->end()
                ->floatNode('requestTimeout')->defaultValue(self::REQUEST_TIMEOUT)->end()
            ->end();

        return $treeBuilder;
    }
}
