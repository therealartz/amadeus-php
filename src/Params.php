<?php declare(strict_types=1);

namespace Amadeus;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class Params
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiSecret;

    /** @var bool */
    private $isProductionEnvironment;

    /** @var LoggerInterface|null */
    private $logger;

    /** @var CacheInterface|null */
    private $cache;

    public function __construct(
        string $apiKey,
        string $apiSecret,
        bool $isProductionEnvironment = false,
        ?LoggerInterface $logger = null,
        ?CacheInterface $cache = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->isProductionEnvironment = $isProductionEnvironment;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    public function setApiSecret(string $apiSecret): void
    {
        $this->apiSecret = $apiSecret;
    }

    public function isProductionEnvironment(): bool
    {
        return $this->isProductionEnvironment;
    }

    public function setIsProductionEnvironment(bool $isProductionEnvironment): void
    {
        $this->isProductionEnvironment = $isProductionEnvironment;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    public function setCache(?CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
