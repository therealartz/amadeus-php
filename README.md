# Amadeus PHP


[![Latest Stable Version](https://poser.pugx.org/therealartz/amadeus-php/v/stable)](https://packagist.org/packages/therealartz/amadeus-php)

This library provides implementation of Client to interact with Amadeus Self-Service API on PHP.

## Installation via Composer

`composer require therealartz/amadeus-php`

## Quickstart

### Setup client

```php
use Amadeus\Client;

$apiKey = 'api_key';
$apiSecret = 'api_secret';

$client = new Client($apiKey, $apiSecret);
```

Authentication will be performed before first request, but token needs to be stored on application side and putted in Client on each request.

// TODO: Provide some example


### Shopping Flight Offers

```php
use Amadeus\Request\Shopping\FlightOffersRequestOptions;

$request = new FlightOffersRequestOptions(
    $origin = 'JFK',
    $destination = 'ORD',
    $departureDate = \DateTimeImmutable::createFromFormat('Y-m-d', '2020-03-01'), // Any \DateTimeInterface
    $returnDate = null,
    $adults = 2,
    $children = 0,
    $infants = 0,
    $seniors = 0,
    $travelClass = null,
    $nonStop = false,
    $currency = 'USD',
    $maxResults = 50,
    $maxPrice = null,
    $arrivalBy = null,
    $returnBy = null,
    $includeAirlines = [],
    $excludeAirlines = []
);

$results = $client->shoppingFlightOffers($request);
```

### Logging

To enable library logging when creating Client pass an instance of `Psr\Log\LoggerInterface` as fourth argument (_optional_):

```php
use Amadeus\Client;
use Monolog\Logger;

$client = new Client(
    $apiKey = 'api_key',
    $apiSecret = 'api_secret',
    $isProduction = false,
    $logger = new Logger(
        'amadeus', 
        [
            new Monolog\Handler\StreamHandler(__DIR__ . '/var/log/amadeus.log')
        ]
    ),
);
```

### Caching

To enable built-in library cache when creating Client pass an instance of `Symfony\Contracts\Cache\CacheInterface` (PSR-16 not supported yet) as fifth argument (_optional_):

```php
use Amadeus\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$client = new Client(
    $apiKey = 'api_key',
    $apiSecret = 'api_secret',
    $isProduction = false,
    $logger = null,
    $cache = new FilesystemAdapter('amadeus', 0, __DIR__ . '/var/cache'),
);

```