<?php declare(strict_types=1);

namespace Amadeus\Contract;

use Symfony\Contracts\Cache\CacheInterface;

interface CacheAwareInterface
{
    public function setCache(CacheInterface $cache);
}
