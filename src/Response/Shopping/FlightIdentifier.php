<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class FlightIdentifier implements Arrayable
{
    private $carrierCode;

    private $flightNumber;

    public function __construct(string $carrierCode, string $flightNumber)
    {
        $this->carrierCode = $carrierCode;
        $this->flightNumber = $flightNumber;
    }

    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    public function getFlightNumber(): string
    {
        return $this->flightNumber;
    }

    public function toArray(): array
    {
        return [
            'carrier' => $this->getCarrierCode(),
            'flightNumber' => $this->getFlightNumber(),
        ];
    }
}
