<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;
use DateInterval;

class FlightSegment implements Arrayable
{
    private $departure;

    private $arrival;

    private $flightIdentifier;

    private $aircraft;

    private $operatingFlightIdentifier;

    private $duration;

    public function __construct(
        Gateway $departure,
        Gateway $arrival,
        FlightIdentifier $flightIdentifier,
        Aircraft $aircraft,
        FlightIdentifier $operatingFlightIdentifier,
        DateInterval $duration
    ) {
        $this->departure = $departure;
        $this->arrival = $arrival;
        $this->flightIdentifier = $flightIdentifier;
        $this->aircraft = $aircraft;
        $this->operatingFlightIdentifier = $operatingFlightIdentifier;
        $this->duration = $duration;
    }

    public function getDeparture(): Gateway
    {
        return $this->departure;
    }

    public function getArrival(): Gateway
    {
        return $this->arrival;
    }

    public function getFlightIdentifier(): FlightIdentifier
    {
        return $this->flightIdentifier;
    }

    public function getAircraft(): Aircraft
    {
        return $this->aircraft;
    }

    public function getOperatingFlightIdentifier(): FlightIdentifier
    {
        return $this->operatingFlightIdentifier;
    }

    public function getDuration(): DateInterval
    {
        return $this->duration;
    }

    public function toArray(): array
    {
        return [
            'departure' => $this->getDeparture()->toArray(),
            'arrival' => $this->getArrival()->toArray(),
            'flightIdentifier' => $this->getFlightIdentifier()->toArray(),
            'aircraft' => $this->getAircraft()->toArray(),
            'operatingFlightIdentifier' => $this->getOperatingFlightIdentifier()->toArray(),
            'duration' => $this->getDuration()->format('%hh %im'),
        ];
    }
}
