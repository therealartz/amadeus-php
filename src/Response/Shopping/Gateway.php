<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;
use DateTimeImmutable;

class Gateway implements Arrayable
{
    private $iataCode;

    private $terminal;

    private $at;

    public function __construct(string $iataCode, string $terminal, DateTimeImmutable $at)
    {
        $this->iataCode = $iataCode;
        $this->terminal = $terminal;
        $this->at = $at;
    }

    public function getIataCode(): string
    {
        return $this->iataCode;
    }

    public function getTerminal(): string
    {
        return trim($this->terminal);
    }

    public function getAt(): DateTimeImmutable
    {
        return $this->at;
    }

    public function toArray(): array
    {
        return [
            'iataCode' => $this->getIataCode(),
            'terminal' => $this->getTerminal(),
            'at' => $this->getAt()->format('d M Y h:i A'),
            'timezone' => $this->getAt()->getTimezone()->getName(),
        ];
    }
}
