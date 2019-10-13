<?php declare(strict_types=1);

namespace Amadeus;

use Amadeus\Auth;
use Amadeus\Contract\CacheAwareInterface;
use Amadeus\Request;
use Amadeus\Response;
use Amadeus\Response\Mapper\FlightOfferMapper;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class Client implements LoggerAwareInterface, CacheAwareInterface
{
    private const DEV_ENDPOINT = 'https://test.api.amadeus.com';

    private const PROD_ENDPOINT = '';

    private const TOKEN_CACHE_KEY = 'amadeus-auth-token';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiSecret;

    /** @var bool */
    private $isProductionEnvironment;

    /** @var HttpClient */
    private $http;

    /** @var Auth\Token */
    private $token;

    /** @var LoggerInterface */
    private $logger;

    /** @var CacheInterface */
    private $cache;

    public function __construct(
        string $apiKey,
        string $apiSecret,
        bool $isProductionEnvironment = false,
        LoggerInterface $logger = null,
        CacheInterface $cache = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->isProductionEnvironment = $isProductionEnvironment;
        $this->logger = $logger;
        $this->cache = $cache;

        $this->http = $this->createHttpClient($logger);
    }

    public function getToken(): Auth\Token
    {
        if ($this->cache instanceof CacheInterface) {
            /** @var CacheItemInterface $item */
            $item = $this->cache->getItem(self::TOKEN_CACHE_KEY);
            /** @var Auth\Token $token */
            $this->token = $item->get();
        }

        if ($this->token === null || $this->token->needsRefresh()) {
            $this->setToken($this->authorize());
        }

        return $this->token;
    }

    public function setToken(Auth\Token $token): void
    {
        if ($this->cache instanceof CacheInterface) {
            /** @var CacheItemInterface $cacheItem */
            $cacheItem = $this->cache->getItem(self::TOKEN_CACHE_KEY);

            $expiresAt = new \DateTimeImmutable('@' . $token->getExpiresAt());

            $cacheItem->expiresAt($expiresAt);
            $cacheItem->set($token);

            $this->cache->save($cacheItem);
        }

        $this->token = $token;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->http = $this->createHttpClient($logger);
    }

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Request\Shopping\FlightOffersRequestOptions $requestOptions
     * @return Response\Shopping\FlightOffer[]
     */
    public function shoppingFlightOffers(Request\Shopping\FlightOffersRequestOptions $requestOptions): array
    {
        $response = $this->http->get('v1/shopping/flight-offers', [
            'headers' => [
                'Authorization' => $this->getToken()->getHeaderString(),
            ],
            'query' => $requestOptions->getArrayForQuery(),
        ]);

        $result = $response->getBody()->__toString();

        $this->logErrorsIfExist($result);

        $mapper = new FlightOfferMapper();

        return $mapper->mapJsonArray($result);
    }

    private function authorize(): Auth\Token
    {
        $response = $this->http->post('/v1/security/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->apiKey,
                'client_secret' => $this->apiSecret,
            ],
        ]);


        $result = \json_decode($response->getBody()->__toString());

        $token = new Auth\Token(
            $result->username,
            $result->application_name,
            $result->client_id,
            $result->token_type,
            $result->access_token,
            (new \DateTimeImmutable())->add(new \DateInterval("PT{$result->expires_in}S"))->getTimestamp(),
            $result->state,
            $result->scope,
        );

        return $token;
    }

    private function createHttpClient(?LoggerInterface $logger): HttpClient
    {
        $httpHandler = HandlerStack::create();
        if ($logger !== null) {
            $this->logger = $logger;
            $httpHandler->push(
                Middleware::log($this->logger, new MessageFormatter(MessageFormatter::DEBUG)),
            );
        }

        return new HttpClient([
            'base_uri' => $this->isProductionEnvironment ? self::PROD_ENDPOINT : self::DEV_ENDPOINT,
            'http_errors' => false,
            'handler' => $httpHandler,
        ]);
    }

    private function logErrorsIfExist(string $result): void
    {
        $response = \json_decode($result);

        if (\property_exists($response, 'errors')) {
            $errorResponse = $response->errors[0];
            $error = new Response\Error(
                $errorResponse->status,
                $errorResponse->code,
                $errorResponse->title,
                $errorResponse->detail,
                $errorResponse->source,
            );

            if ($this->logger instanceof LoggerInterface) {
                $this->logger->info($error->getTitle(), $error->toArray());
            }
        }
    }
}
