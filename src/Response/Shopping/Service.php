<?php declare(strict_types=1);

namespace Amadeus\Response\Shopping;

use Amadeus\Contract\Arrayable;

class Service implements Arrayable
{
    /** @var Segment[] */
    private $segments;

    /**
     * @param Segment[] $segments
     */
    public function __construct(array $segments)
    {
        $this->segments = $segments;
    }

    /**
     * @return Segment[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }

    public function toArray(): array
    {
        return [
            'segments' => \array_map(function (Segment $segment) {
                return $segment->toArray();
            }, $this->getSegments()),
        ];
    }
}
