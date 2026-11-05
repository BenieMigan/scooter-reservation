<?php

namespace Src\Application\Scooter\Query\GetAvailableScooters;

interface ScooterCatalogReaderInterface
{
    /** @return AvailableScooterView[] */
    public function findAvailableFor(int $userId): array;
}