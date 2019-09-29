<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class PricingDetail implements Arrayable
{
    private $travelClass;

    private $fareClass;

    private $availability;

    private $fareBasis;

    public function __construct(string $travelClass, string $fareClass, int $availability, string $fareBasis)
    {
        $this->travelClass = $travelClass;
        $this->fareClass = $fareClass;
        $this->availability = $availability;
        $this->fareBasis = $fareBasis;
    }

    public function getTravelClass(): string
    {
        return $this->travelClass;
    }

    public function getFareClass(): string
    {
        return $this->fareClass;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    public function getFareBasis(): string
    {
        return $this->fareBasis;
    }

    public function toArray(): array
    {
        return [
            'travelClass' => $this->getTravelClass(),
            'fareClass' => $this->getFareClass(),
            'availability' => $this->getAvailability(),
            'fareBasis' => $this->getFareBasis(),
        ];
    }
}
