<?php declare(strict_types=1);

namespace Amadeus\Response;

use Amadeus\Contract\Arrayable;

class Error implements Arrayable
{
    protected $status;

    protected $code;

    protected $title;

    protected $detail;

    protected $source;

    public function __construct(int $status, int $code, string $title, string $detail, \stdClass $source)
    {
        $this->status = $status;
        $this->code = $code;
        $this->title = $title;
        $this->detail = $detail;
        $this->source = $source;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getSource(): \stdClass
    {
        return $this->source;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'code' => $this->getCode(),
            'title' => $this->getTitle(),
            'detail' => $this->getDetail(),
            'source' => (array)$this->getSource(),
        ];
    }
}
