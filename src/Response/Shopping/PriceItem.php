<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class PriceItem implements Arrayable
{
    private $total;

    private $totalTaxes;

    public function __construct(string $total, string $totalTaxes)
    {
        $this->total = $total;
        $this->totalTaxes = $totalTaxes;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function getTotalTaxes(): string
    {
        return $this->totalTaxes;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->getTotal(),
            'totalTaxes' => $this->getTotalTaxes(),
        ];
    }
}
