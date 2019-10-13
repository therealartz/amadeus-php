<?php declare(strict_types=1);

namespace Amadeus\Response;

class Response
{
    protected $error;

    public function __construct(?Error $error)
    {
        $this->error = $error;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function getErrorArray(): ?array
    {
        return $this->getError() ? $this->getError()->toArray() : null;
    }
}
