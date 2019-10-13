<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class OfferItem implements Arrayable
{
    /** @var Service[] */
    private $services;

    private $price;

    private $pricePerAdult;

    private $pricePerChild;

    /**
     * @param Service[] $services
     * @param PriceItem $price
     * @param PriceItem $pricePerAdult
     * @param PriceItem|null $pricePerChild
     */
    public function __construct(array $services, PriceItem $price, PriceItem $pricePerAdult, ?PriceItem $pricePerChild)
    {
        $this->services = $services;
        $this->price = $price;
        $this->pricePerAdult = $pricePerAdult;
        $this->pricePerChild = $pricePerChild;
    }

    /**
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function getPrice(): PriceItem
    {
        return $this->price;
    }

    public function getPricePerAdult(): PriceItem
    {
        return $this->pricePerAdult;
    }

    public function getPricePerChild(): ?PriceItem
    {
        return $this->pricePerChild;
    }

    public function toArray(): array
    {
        $array = [
            'services' => \array_map(function (Service $service) {
                return $service->toArray();
            }, $this->getServices()),
            'price' => $this->getPrice()->toArray(),
            'pricePerAdult' => $this->getPricePerAdult()->toArray(),
        ];

        if ($this->getPricePerChild() !== null) {
            $array['pricePerChild'] = $this->getPricePerChild()->toArray();
        }

        return $array;
    }
}
