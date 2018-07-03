<?php declare(strict_types=1);
/**
 * This file belongs to Bandit. All rights reserved
 */

namespace AwsSecretsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <themizzi@me.com>
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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aws_secrets');

        $rootNode->children()
            ->scalarNode('aws_region')->end()
            ->scalarNode('aws_version')->defaultValue('latest')->end()
            ->scalarNode('store_prefix')->end()
            ->booleanNode('ignore')->defaultFalse()->end()
            ->scalarNode('aws_key')->end()
            ->scalarNode('aws_secret')->end()
            ->scalarNode('delimiter')->defaultValue(',')->end();

        return $treeBuilder;
    }
}