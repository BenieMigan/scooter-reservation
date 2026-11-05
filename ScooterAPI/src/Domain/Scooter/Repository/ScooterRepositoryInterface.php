<?php

namespace Src\Domain\Scooter\Repository;

use Src\Domain\Scooter\Entity\Scooter;

interface ScooterRepositoryInterface
{
    public function save(Scooter $scooter): void;

    public function existsByPlate(string $plate): bool;

    public function findById(int $id): ?Scooter;

    public function grantAccess(int $scooterId, int $userId): void;

    public function hasAccess(int $scooterId, int $userId): bool;

    public function findByOwnerId(int $ownerId): array;
}

