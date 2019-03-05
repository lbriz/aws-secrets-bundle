<?php
/**
 * This file belongs to Casechek. All rights reserved
 */

namespace AwsSecretsBundle\Provider;

use Psr\Cache\CacheItemPoolInterface;

/**
 * @author  Joe Mizzi <joe@casechek.com>
 */
class AwsSecretsCachedEnvVarProvider implements AwsSecretsEnvVarProviderInterface
{
    const CACHE_KEY_PREFIX = 'aws_secret';

    private $cacheItemPool;
    private $decorated;
    private $ttl;

    public function __construct(CacheItemPoolInterface $cacheItemPool, AwsSecretsEnvVarProviderInterface $decorated, $ttl = 60)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->decorated = $decorated;
        $this->ttl = $ttl;
    }

    public function get($name)
    {
        $cacheKey = self::CACHE_KEY_PREFIX.'.'.md5($name);
        $cacheItem = $this->cacheItemPool->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $value = $this->decorated->get($name);

        if (isset($cacheItem)) {
            $cacheItem->set($value);
            $cacheItem->expiresAfter($this->ttl);
            $this->cacheItemPool->save($cacheItem);
        }

        return $value;
    }
}
