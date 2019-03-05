<?php

namespace AwsSecretsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aws_secrets');

        $rootNode->children()
            ->arrayNode('client_config')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('region')->defaultNull()->end()
                    ->scalarNode('version')->defaultValue('latest')->end()
                    ->arrayNode('credentials')
                        ->children()
                            ->scalarNode('key')->defaultNull()->end()
                            ->scalarNode('secret')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->enumNode('cache')->values(['apcu', 'filesystem', 'array'])->defaultValue('array')->end()
            ->scalarNode('ttl')->defaultValue(60)->end()
            ->scalarNode('delimiter')->defaultValue(',')->end()
            ->scalarNode('ignore')->defaultFalse()->end();

        return $treeBuilder;
    }
}
