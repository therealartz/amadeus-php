<?php declare(strict_types=1);

namespace Amadeus\Tests;

use Amadeus\CacheableTokenClient;
use Amadeus\Client;
use Amadeus\ClientFactory;
use Amadeus\Params;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class ClientFactoryTest extends TestCase
{
    public function testCreatesNonCacheableTokenClient(): void
    {
        $params = new Params('key', 'secret', false);

        $client = ClientFactory::create($params);

        self::assertInstanceOf(Client::class, $client);
    }

    public function testCreatesCacheableTokenClient(): void
    {
        $params = new Params(
            'key',
            'secret',
            false,
            null,
            new ArrayAdapter(),
        );

        $client = ClientFactory::create($params);

        self::assertInstanceOf(CacheableTokenClient::class, $client);
        self::assertInstanceOf(Client::class, $client);
    }
}
