<?php declare(strict_types=1);

namespace Amadeus\Request;

interface RequestOptionsInterface
{
    public function getArrayForQuery(): array;
}