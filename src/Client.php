<?php declare(strict_types=1);

namespace Amadeus;

use Amadeus\Auth;
use Amadeus\Request;
use Amadeus\Response;
use Amadeus\Response\Mapper\FlightOfferMapper;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Client implements LoggerAwareInterface
{
    protected const DEV_ENDPOINT = 'https://test.api.amadeus.com';

    protected const PROD_ENDPOINT = '';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiSecret;

    /** @var bool */
    private $isProductionEnvironment;

    /** @var HttpClient */
    protected $http;

    /** @var Auth\Token */
    protected $token;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(Params $params)
    {
        $this->apiKey = $params->getApiKey();
        $this->apiSecret = $params->getApiSecret();
        $this->isProductionEnvironment = $params->isProductionEnvironment();
        $this->logger = $params->getLogger();

        $this->http = $this->createHttpClient($params->getLogger());
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

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->http = $this->createHttpClient($logger);
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

    protected function authorize(): Auth\Token
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

    protected function createHttpClient(?LoggerInterface $logger): HttpClient
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

    protected function logErrorsIfExist(string $result): void
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
