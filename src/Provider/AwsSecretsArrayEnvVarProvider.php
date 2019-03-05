<?php
/**
 * This file belongs to Casechek. All rights reserved
 */

namespace AwsSecretsBundle\Provider;

/**
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretsArrayEnvVarProvider implements AwsSecretsEnvVarProviderInterface
{
    private $values = [];
    private $decorated;

    public function __construct(AwsSecretsEnvVarProviderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function get($name)
    {
        if (!isset($this->values[$name])) {
            $this->values[$name] = $this->decorated->get($name);
        }

        return $this->values[$name];
    }
}
