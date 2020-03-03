<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('answear_luigis_box');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('host')->end()
                ->scalarNode('publicKey')->end()
                ->scalarNode('privateKey')->end()
            ->end();

        return $treeBuilder;
    }
}
