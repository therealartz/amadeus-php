<?php declare(strict_types=1);

namespace Amadeus\Response\Mapper;

use Amadeus\Contract\Arrayable;
use Amadeus\Exception\MapperException;
use Amadeus\Response\Shopping;

class FlightOfferMapper implements MapperInterface
{
    protected const AMADEUS_TYPE = 'flight-offer';

    public function mapJsonArray(string $json): array
    {
        $response = \json_decode($json, true);

        $results = [];

        $dictionaries = $response['dictionaries'] ?? [];

        if (\is_array($response['data'] ?? null)) {
            foreach ($response['data'] as $item) {
                if ($item['type'] === self::AMADEUS_TYPE) {
                    try {
                        $results[] = $this->flightOfferToClass($item, $dictionaries);
                    } catch (\Throwable $exception) {
                        throw new MapperException('Mapping error', 0, $exception);
                    }
                }
            }
        }

        return $results;
    }

    public function mapJson(string $json): ?Shopping\FlightOffer
    {
        $response = \json_decode($json, true);

        if ($response['type'] !== self::AMADEUS_TYPE) {
            throw new MapperException(\sprintf(
                __METHOD__ . ': invalid argument passed type %s. Expected: %s',
                $response['type'],
                self::AMADEUS_TYPE
            ));
        }

        return $this->flightOfferToClass($response);
    }

    public function mapObjectsArrayToArrays(array $objects): array
    {
        $items = [];

        foreach ($objects as $object) {
            if ($object instanceof Shopping\FlightOffer && $object instanceof Arrayable) {
                $items[] = $object->toArray();
            } else {
                throw new MapperException(\sprintf(
                    __METHOD__ . ': invalid argument passed type %s. Expected: %s and %s',
                    \get_class($object),
                    Shopping\FlightOffer::class,
                    Arrayable::class,
                ));
            }
        }

        return $items;
    }

    protected function flightOfferToClass(array $data, array $dictionaries = []): Shopping\FlightOffer
    {
        $offerItems = [];

        foreach ($data['offerItems'] as $offerItem) {
            $services = [];

            foreach ($offerItem['services'] as $service) {
                $segments = [];

                foreach ($service['segments'] as $segment) {
                    $aircraftCode = $segment['flightSegment']['aircraft']['code'];

                    $operatingCarrier = $segment['flightSegment']['operating']['carrierCode']
                        ?? $segment['flightSegment']['carrierCode'];

                    $flightSegment = new Shopping\FlightSegment(
                        new Shopping\Gateway(
                            $segment['flightSegment']['departure']['iataCode'],
                            $segment['flightSegment']['departure']['terminal'] ?? '',
                            new \DateTimeImmutable($segment['flightSegment']['departure']['at']),
                        ),
                        new Shopping\Gateway(
                            $segment['flightSegment']['arrival']['iataCode'],
                            $segment['flightSegment']['arrival']['terminal'] ?? '',
                            new \DateTimeImmutable($segment['flightSegment']['arrival']['at']),
                        ),
                        new Shopping\FlightIdentifier(
                            $segment['flightSegment']['carrierCode'],
                            $segment['flightSegment']['number'],
                        ),
                        new Shopping\Aircraft(
                            $aircraftCode,
                            $dictionaries['aircraft'][$aircraftCode] ?? '',
                        ),
                        new Shopping\FlightIdentifier(
                            $operatingCarrier,
                            $segment['flightSegment']['operating']['number'],
                        ),
                        new \DateInterval('P' . $segment['flightSegment']['duration']),
                    );

                    $pricingDetailPerAdult = new Shopping\PricingDetail(
                        $segment['pricingDetailPerAdult']['travelClass'],
                        $segment['pricingDetailPerAdult']['fareClass'],
                        $segment['pricingDetailPerAdult']['availability'],
                        $segment['pricingDetailPerAdult']['fareBasis'],
                    );

                    if (\array_key_exists('pricingDetailPerChild', $segment)) {
                        $pricingDetailPerChild = new Shopping\PricingDetail(
                            $segment['pricingDetailPerChild']['travelClass'],
                            $segment['pricingDetailPerChild']['fareClass'],
                            $segment['pricingDetailPerChild']['availability'],
                            $segment['pricingDetailPerChild']['fareBasis'],
                        );
                    } else {
                        $pricingDetailPerChild = null;
                    }

                    $segments[] = new Shopping\Segment($flightSegment, $pricingDetailPerAdult, $pricingDetailPerChild);
                }

                $services[] = new Shopping\Service($segments);
            }

            $price = new Shopping\PriceItem($offerItem['price']['total'], $offerItem['price']['totalTaxes']);

            $pricePerAdult = new Shopping\PriceItem(
                $offerItem['pricePerAdult']['total'],
                $offerItem['pricePerAdult']['totalTaxes']
            );

            if (\array_key_exists('pricePerChild', $offerItem)) {
                $pricePerChild = new Shopping\PriceItem(
                    $offerItem['pricePerChild']['total'],
                    $offerItem['pricePerChild']['totalTaxes']
                );
            } else {
                $pricePerChild = null;
            }

            $offerItems[] = new Shopping\OfferItem($services, $price, $pricePerAdult, $pricePerChild);
        }

        return new Shopping\FlightOffer($data['id'], $offerItems);
    }
}
