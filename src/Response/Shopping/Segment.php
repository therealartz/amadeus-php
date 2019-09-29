<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class Segment implements Arrayable
{
    private $flightSegment;

    private $pricingDetailPerAdult;

    private $pricingDetailPerChild;

    public function __construct(
        FlightSegment $flightSegment,
        PricingDetail $pricingDetailPerAdult,
        ?PricingDetail $pricingDetailPerChild
    ) {
        $this->flightSegment = $flightSegment;
        $this->pricingDetailPerAdult = $pricingDetailPerAdult;
        $this->pricingDetailPerChild = $pricingDetailPerChild;
    }

    public function getFlightSegment(): FlightSegment
    {
        return $this->flightSegment;
    }

    public function getPricingDetailPerAdult(): PricingDetail
    {
        return $this->pricingDetailPerAdult;
    }

    public function getPricingDetailPerChild(): ?PricingDetail
    {
        return $this->pricingDetailPerChild;
    }

    public function toArray(): array
    {
        $array = [
            'flightSegment' => $this->getFlightSegment()->toArray(),
            'pricingDetailPerAdult' => $this->getPricingDetailPerAdult()->toArray(),
        ];

        if ($this->getPricingDetailPerChild()) {
            $array['pricingDetailPerChild'] = $this->getPricingDetailPerChild()->toArray();
        }

        return $array;
    }
}
