<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;
use Amadeus\Response\Error;
use Amadeus\Response\Response;

class FlightOffer extends Response implements Arrayable
{
    private $id;

    /** @var OfferItem[] */
    private $offerItems;

    /**
     * @param string $id
     * @param OfferItem[] $offerItems
     * @param Error|null $error
     */
    public function __construct(string $id, array $offerItems, Error $error = null)
    {
        $this->id = $id;
        $this->offerItems = $offerItems;
        parent::__construct($error);
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
            'offerItems' => \array_map(function (OfferItem $offerItem) {
                return $offerItem->toArray();
            }, $this->getOfferItems()),
            'error' => $this->getErrorArray(),
        ];
    }
}
