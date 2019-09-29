<?php declare(strict_types=1);

namespace Amadeus\Response\Mapper;

interface MapperInterface
{
    public function mapJsonArray(string $json): array;

    public function mapJson(string $json);

    public function mapObjectsArrayToArrays(array $objects): array;
}
