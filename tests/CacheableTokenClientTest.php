<?php declare(strict_types=1);

namespace Amadeus\Tests;

use Amadeus\Auth\Token;
use Amadeus\CacheableTokenClient;
use Amadeus\Params;
use GuzzleHttp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CacheableTokenClientTest extends TestCase
{
    /** @var CacheableTokenClient */
    private $client;

    protected function setUp(): void
    {
        $this->client = new CacheableTokenClient(
            new Params(
                'key',
                'secret',
                false,
                null,
                new ArrayAdapter()
            )
        );

        parent::setUp();
    }

    public function testClientFlushesExpiredTokenInCacheAndRetrievesNew(): void
    {
        $expiredToken = new Token(
            'username',
            'amadeus-php',
            'clientId',
            'Bearer',
            'token',
            \time() - 1, // past to flush on check
            '',
            '',
        );

        $this->client->setToken($expiredToken);

        // HTTP mock
        $response = [
            'type' => 'amadeusOAuth2Token',
            'username' => 'username',
            'application_name' => 'amadeus-php',
            'client_id' => 'clientId',
            'token_type' => 'Bearer',
            'access_token' => 'new-token',
            'expires_in' => 1799,
            'state' => 'approved',
            'scope' => '',
        ];

        $httpMock = new GuzzleHttp\Client([
            'handler' => GuzzleHttp\HandlerStack::create(
                new GuzzleHttp\Handler\MockHandler([
                    new GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], \json_encode($response)),
                ])
            ),
        ]);

        $reflection = new \ReflectionClass($this->client);
        $property = $reflection->getProperty('http');

        $property->setAccessible(true);
        $property->setValue($this->client, $httpMock);

        self::assertInstanceOf(Token::class, $this->client->getToken());
        self::assertEquals('username', $this->client->getToken()->getUsername());
        self::assertEquals('amadeus-php', $this->client->getToken()->getApplicationName());
        self::assertEquals('clientId', $this->client->getToken()->getClientId());
        self::assertEquals('new-token', $this->client->getToken()->getAccessToken());
    }

    public function testClientStoresTokenInCache(): void
    {
        $token = new Token(
            'username',
            'amadeus-php',
            'clientId',
            'Bearer',
            'token',
            \time() + 500, // must be future otherwise will be flushed on check
            '',
            '',
        );

        $this->client->setToken($token);

        self::assertInstanceOf(Token::class, $this->client->getToken());
        self::assertEquals('username', $this->client->getToken()->getUsername());
        self::assertEquals('amadeus-php', $this->client->getToken()->getApplicationName());
        self::assertEquals('clientId', $this->client->getToken()->getClientId());
        self::assertEquals('token', $this->client->getToken()->getAccessToken());
    }
}
