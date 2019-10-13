<?php declare(strict_types=1);

namespace Amadeus;

class ClientFactory
{
    public static function create(Params $params): Client
    {
        if ($params->getCache() !== null) {
            $client = new CacheableTokenClient($params);
        } else {
            $client = new Client($params);
        }

        return $client;
    }
}
