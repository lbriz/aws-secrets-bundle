<?php

namespace AwsSecretsBundle\DependencyInjection;

use Aws\SecretsManager\SecretsManagerClient;
use AwsSecretsBundle\AwsSecretsEnvVarProcessor;
use AwsSecretsBundle\Command\AwsSecretValueCommand;
use AwsSecretsBundle\Provider\AwsSecretsArrayEnvVarProvider;
use AwsSecretsBundle\Provider\AwsSecretsCachedEnvVarProvider;
use AwsSecretsBundle\Provider\AwsSecretsEnvVarProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AwsSecretsExtension
 * @package AwsSecretsBundle\DependencyInjection
 * @author  Joe Mizzi <joe@casechek.com>
 *
 * @codeCoverageIgnore
 */
class AwsSecretsExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);

        $container->setParameter('aws_secrets.ttl', $configs['ttl']);
        $container->setParameter('aws_secrets.ignore', $configs['ignore']);
        $container->setParameter('aws_secrets.delimiter', $configs['delimiter']);

        $container->register('aws_secrets.secrets_manager_client', SecretsManagerClient::class)
            ->setLazy(true)
            ->addArgument($configs['client_config'])
            ->setPublic(false);

        $container->setAlias('aws_secrets.client', 'aws_secrets.secrets_manager_client')
            ->setPublic(true);

        if ($configs['cache'] === 'apcu') {
            $definition = new ChildDefinition('cache.adapter.apcu');
        } elseif ($configs['cache'] === 'filesystem') {
            $definition = new ChildDefinition('cache.adapter.filesystem');
        } else {
            $definition = new Definition(ArrayAdapter::class);
        }

        $definition->addTag('cache.pool');
        $container->setDefinition('aws_secrets.cache', $definition);

        $container->register('aws_secrets.env_var_provider', AwsSecretsEnvVarProvider::class)
            ->setArgument('$secretsManagerClient', new Reference('aws_secrets.client'))
            ->setPublic(false);

        $container->register('aws_secrets.env_var_cached_provider', AwsSecretsCachedEnvVarProvider::class)
            ->setArgument('$cacheItemPool', new Reference('aws_secrets.cache'))
            ->setArgument('$decorated', new Reference('aws_secrets.env_var_provider'))
            ->setArgument('$ttl', $container->getParameter('aws_secrets.ttl'))
            ->setPublic(false);

        $container->register('aws_secrets.env_var_array_provider', AwsSecretsArrayEnvVarProvider::class)
            ->setArgument('$decorated', new Reference('aws_secrets.env_var_cached_provider'))
            ->setPublic(false);

        $container->register('aws_secrets.env_var_processor', AwsSecretsEnvVarProcessor::class)
            ->setArgument('$provider', new Reference('aws_secrets.env_var_array_provider'))
            ->setArgument('$ignore', $container->getParameter('aws_secrets.ignore'))
            ->setArgument('$delimiter', $container->getParameter('aws_secrets.delimiter'))
            ->setPublic(false)
            ->addTag('container.env_var_processor');
    }
}
