<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class FlightOffer implements Arrayable
{
    private $id;

    /** @var OfferItem[] */
    private $offerItems;

    /**
     * @param string $id
     * @param OfferItem[] $offerItems
     */
    public function __construct(string $id, array $offerItems)
    {
        $this->id = $id;
        $this->offerItems = $offerItems;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return OfferItem[]
     */
    public function getOfferItems(): array
    {
        return $this->offerItems;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'offerItems' => array_map(function (OfferItem $offerItem) {
                return $offerItem->toArray();
            }, $this->getOfferItems()),
        ];
    }


}
