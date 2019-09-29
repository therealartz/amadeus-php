<?php declare(strict_types=1);

namespace Amadeus\Request\Shopping;

use Amadeus\Request\RequestOptionsInterface;
use DateTimeInterface;

class FlightOffersRequestOptions implements RequestOptionsInterface
{
    public const TRAVEL_CLASS_ECONOMY = 'ECONOMY';
    public const TRAVEL_CLASS_PREMIUM_ECONOMY = 'PREMIUM_ECONOMY';
    public const TRAVEL_CLASS_BUSINESS = 'BUSINESS';
    public const TRAVEL_CLASS_FIRST = 'FIRST';

    private $origin;

    private $destination;

    private $departureDate;

    private $returnDate;

    private $adults;

    private $children;

    private $infants;

    private $seniors;

    private $travelClass;

    private $nonStop;

    private $currency;

    private $maxResults;

    private $maxPrice;

    private $arrivalBy;

    private $returnBy;

    private $includeAirlines;

    private $excludeAirlines;

    /**
     * FlightOffersRequestOptions constructor.
     * @param string $origin
     * @param string $destination
     * @param DateTimeInterface $departureDate
     * @param DateTimeInterface|null $returnDate
     * @param int $adults
     * @param int $children
     * @param int $infants
     * @param int $seniors
     * @param string|null $travelClass
     * @param bool $nonStop
     * @param string $currency
     * @param int $maxResults
     * @param int|null $maxPrice
     * @param DateTimeInterface|null $arrivalBy
     * @param DateTimeInterface|null $returnBy
     * @param string[] $includeAirlines
     * @param string[] $excludeAirlines
     */
    public function __construct(
        string $origin,
        string $destination,
        DateTimeInterface $departureDate,
        ?DateTimeInterface $returnDate = null,
        int $adults = 1,
        int $children = 0,
        int $infants = 0,
        int $seniors = 0,
        ?string $travelClass = null,
        bool $nonStop = false,
        string $currency = 'USD',
        int $maxResults = 50,
        int $maxPrice = null,
        ?DateTimeInterface $arrivalBy = null,
        ?DateTimeInterface $returnBy = null,
        array $includeAirlines = [],
        array $excludeAirlines = []
    ) {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->departureDate = $departureDate;
        $this->returnDate = $returnDate;
        $this->adults = $adults;
        $this->children = $children;
        $this->infants = $infants;
        $this->seniors = $seniors;
        $this->travelClass = $travelClass;
        $this->nonStop = $nonStop;
        $this->currency = $currency;
        $this->maxResults = $maxResults;
        $this->maxPrice = $maxPrice;
        $this->arrivalBy = $arrivalBy;
        $this->returnBy = $returnBy;
        $this->includeAirlines = $includeAirlines;
        $this->excludeAirlines = $excludeAirlines;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): void
    {
        $this->destination = $destination;
    }

    public function getDepartureDate(): DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(DateTimeInterface $departureDate): void
    {
        $this->departureDate = $departureDate;
    }

    public function getReturnDate(): ?DateTimeInterface
    {
        return $this->returnDate;
    }

    public function setReturnDate(?DateTimeInterface $returnDate): void
    {
        $this->returnDate = $returnDate;
    }

    public function getAdults(): int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): void
    {
        $this->adults = $adults;
    }

    public function getChildren(): int
    {
        return $this->children;
    }

    public function setChildren(int $children): void
    {
        $this->children = $children;
    }

    public function getInfants(): int
    {
        return $this->infants;
    }

    public function setInfants(int $infants): void
    {
        $this->infants = $infants;
    }

    public function getSeniors(): int
    {
        return $this->seniors;
    }

    public function setSeniors(int $seniors): void
    {
        $this->seniors = $seniors;
    }

    public function getTravelClass(): ?string
    {
        return $this->travelClass;
    }

    public function setTravelClass(?string $travelClass): void
    {
        $this->travelClass = $travelClass;
    }

    public function isNonStop(): bool
    {
        return $this->nonStop;
    }

    public function setNonStop(bool $nonStop): void
    {
        $this->nonStop = $nonStop;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function setMaxResults(int $maxResults): void
    {
        $this->maxResults = $maxResults;
    }

    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?int $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }

    public function getArrivalBy(): ?DateTimeInterface
    {
        return $this->arrivalBy;
    }

    public function setArrivalBy(?DateTimeInterface $arrivalBy): void
    {
        $this->arrivalBy = $arrivalBy;
    }

    public function getReturnBy(): ?DateTimeInterface
    {
        return $this->returnBy;
    }

    public function setReturnBy(?DateTimeInterface $returnBy): void
    {
        $this->returnBy = $returnBy;
    }

    /**
     * @return string[]
     */
    public function getIncludeAirlines(): array
    {
        return $this->includeAirlines;
    }

    /**
     * @param string[] $includeAirlines
     */
    public function setIncludeAirlines(array $includeAirlines): void
    {
        $this->includeAirlines = $includeAirlines;
    }

    /**
     * @return string[]
     */
    public function getExcludeAirlines(): array
    {
        return $this->excludeAirlines;
    }

    /**
     * @param string[] $excludeAirlines
     */
    public function setExcludeAirlines(array $excludeAirlines): void
    {
        $this->excludeAirlines = $excludeAirlines;
    }

    public function getArrayForQuery(): array
    {
        $query = [
            'origin' => $this->getOrigin(),
            'destination' => $this->getDestination(),
            'departureDate' => $this->getDepartureDate()->format('Y-m-d'),
            'returnDate' => $this->getReturnDate() ? $this->getReturnDate()->format('Y-m-d') : null,
            'adults' => $this->getAdults(),
            'children' => $this->getChildren(),
            'infants' => $this->getInfants(),
            'seniors' => $this->getSeniors(),
            'travelClass' => $this->getTravelClass(),
            'nonStop' => $this->isNonStop(),
            'currency' => $this->getCurrency(),
            'max' => $this->getMaxResults(),
            'maxPrice' => $this->getMaxPrice(),
            'arrivalBy' => $this->getArrivalBy() ? $this->getArrivalBy()->format(DateTimeInterface::ISO8601) : null,
            'returnBy' => $this->getReturnBy() ? $this->getReturnBy()->format(DateTimeInterface::ISO8601) : null,
            'includeAirlines' => implode(',', $this->getIncludeAirlines()),
            'excludeAirlines' => implode(',', $this->getExcludeAirlines()),
        ];

        return array_filter($query);
    }
}
