<?php

namespace AwsSecretsBundle;

use AwsSecretsBundle\Provider\AwsSecretsEnvVarProviderInterface;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Class AwsSecretsEnvVarProcessor
 * @package AwsSecretsBundle
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretsEnvVarProcessor implements EnvVarProcessorInterface
{
    const AWS_SECRET_ID = 'SecretId';
    const AWS_SECRET_STRING = 'SecretString';

    private $delimiter;
    private $decodedSecrets = [];
    private $ignore;
    private $provider;

    public function __construct(
        AwsSecretsEnvVarProviderInterface $provider,
        $ignore = false,
        $delimiter = ','
    ) {
        $this->ignore = $ignore;
        $this->delimiter = $delimiter;
        $this->provider = $provider;
    }

    /**
     * Returns the value of the given variable as managed by the current instance.
     *
     * @param string $prefix The namespace of the variable
     * @param string $name The name of the variable within the namespace
     * @param \Closure $getEnv A closure that allows fetching more env vars
     *
     * @return mixed
     *
     * @throws RuntimeException on error
     */
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        if ($this->ignore === true) {
            return $getEnv($name);
        }

        $value = $getEnv($name);

        $parts = explode($this->delimiter, $value);
        $result = $this->provider->get($parts[0]);

        if (isset($parts[1])) {
            if (!isset($this->decodedSecrets[$parts[0]])) {
                $this->decodedSecrets[$parts[0]] = json_decode($result, true);
            }

            return (string)$this->decodedSecrets[$parts[0]][$parts[1]];
        }

        return $result;
    }

    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;
    }

    /**
     * @return string[] The PHP-types managed by getEnv(), keyed by prefixes
     * @codeCoverageIgnore
     */
    public static function getProvidedTypes()
    {
        return [
            'aws' => 'bool|int|float|string',
        ];
    }
}
