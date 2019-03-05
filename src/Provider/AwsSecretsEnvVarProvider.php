<?php
/**
 * This file belongs to Casechek. All rights reserved
 */

namespace AwsSecretsBundle\Provider;

use Aws\SecretsManager\SecretsManagerClient;

/**
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretsEnvVarProvider implements AwsSecretsEnvVarProviderInterface
{
    const AWS_SECRET_ID = 'SecretId';
    const AWS_SECRET_STRING = 'SecretString';

    private $secretsManagerClient;

    public function __construct(SecretsManagerClient $secretsManagerClient)
    {
        $this->secretsManagerClient = $secretsManagerClient;
    }

    public function get($name)
    {
        return $this->secretsManagerClient
            ->getSecretValue([self::AWS_SECRET_ID => $name])
            ->get(self::AWS_SECRET_STRING);
    }
}
