<?php

declare(strict_types=1);

namespace Answear\LuigisBoxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const HOST = 'https://live.luigisbox.com';
    public const CONNECTION_TIMEOUT = 4.0;
    public const REQUEST_TIMEOUT = 10.0;
    public const SEARCH_TIMEOUT = 6.0;
    public const SEARCH_CACHE_TIMEOUT = 0;
    public const RECOMMENDATIONS_REQUEST_TIMEOUT = 1.0;
    public const RECOMMENDATIONS_CONNECTION_TIMEOUT = 10.0;

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
                        ->floatNode('searchTimeout')->defaultValue(self::SEARCH_TIMEOUT)->end()
                        ->integerNode('searchCacheTtl')->defaultValue(self::SEARCH_CACHE_TIMEOUT)->end()
                        ->floatNode('recommendationsRequestTimeout')->defaultValue(self::RECOMMENDATIONS_REQUEST_TIMEOUT)->end()
                        ->floatNode('recommendationsConnectionTimeout')->defaultValue(self::RECOMMENDATIONS_CONNECTION_TIMEOUT)->end()
                    ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
