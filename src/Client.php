<?php

namespace Amadeus;

use Amadeus\Auth;
use Amadeus\Request;
use Amadeus\Response;
use Amadeus\Response\Mapper\FlightOfferMapper;
use DateInterval;
use DateTimeImmutable;
use GuzzleHttp\Client as HttpClient;

class Client
{
    private const DEV_ENDPOINT = 'https://test.api.amadeus.com';

    private const PROD_ENDPOINT = '';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiSecret;

    /** @var HttpClient */
    private $http;

    /** @var Auth\Token */
    private $token;

    public function __construct(string $apiKey, string $apiSecret, bool $isProductionEnvironment = false)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        $this->http = new HttpClient([
            'base_uri' => $isProductionEnvironment ? self::PROD_ENDPOINT : self::DEV_ENDPOINT,
        ]);
    }

    public function getToken(): Auth\Token
    {
        if ($this->token === null || $this->token->needsRefresh()) {
            $this->setToken($this->authorize());
        }

        return $this->token;
    }

    public function setToken(Auth\Token $token): void
    {
        $this->token = $token;
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

        $result = $response->getBody()->getContents();

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

        $result = json_decode($response->getBody()->getContents());

        $token = new Auth\Token(
            $result->username,
            $result->application_name,
            $result->client_id,
            $result->token_type,
            $result->access_token,
            (new DateTimeImmutable())->add(new DateInterval("PT{$result->expires_in}S"))->getTimestamp(),
            $result->state,
            $result->scope,
        );

        return $token;
    }
}
