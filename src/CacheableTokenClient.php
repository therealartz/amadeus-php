<?php declare(strict_types=1);

namespace Amadeus;

use Amadeus\Auth\Token;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CacheableTokenClient extends Client
{
    private const TOKEN_CACHE_KEY = 'amadeus-auth-token';

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(Params $params)
    {
        parent::__construct($params);

        $this->cache = $params->getCache();
    }

    public function getToken(): Token
    {
        /** @var CacheItemInterface $item */
        $item = $this->cache->getItem(self::TOKEN_CACHE_KEY);
        /** @var Auth\Token $token */
        $this->token = $item->get();

        return parent::getToken();
    }

    public function setToken(Token $token): void
    {
        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->cache->getItem(self::TOKEN_CACHE_KEY);

        $expiresAt = new \DateTimeImmutable('@' . $token->getExpiresAt());

        $cacheItem->expiresAt($expiresAt);
        $cacheItem->set($token);

        $this->cache->save($cacheItem);

        parent::setToken($token);
    }
}
