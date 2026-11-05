<?php

namespace Src\Application\Scooter\Query\GetMyScooters;

final readonly class GetMyScootersQuery
{
    public function __construct(
        public int $ownerId
    ) {}
}