<?php

namespace Src\Application\Scooter\Query\GetAvailableScooters;

final readonly class GetAvailableScootersQuery
{
    public function __construct(public int $userId) {}
}