<?php

namespace Src\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Src\Domain\Scooter\Entity\Scooter;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;

class DoctrineScooterRepository implements ScooterRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function save(Scooter $scooter): void
    {
        $this->connection->insert('scooters', [
            'owner_id' => $scooter->getOwnerId(),
            'model'    => $scooter->getModel(),
            'plate'    => $scooter->getPlate(),
        ]);
    }

    public function existsByPlate(string $plate): bool
    {
        return $this->connection->fetchOne(
            'SELECT id FROM scooters WHERE plate = ?',
            [$plate]
        ) !== false;
    }

    public function findById(int $id): ?Scooter
{
    $row = $this->connection->fetchAssociative(
        'SELECT id, owner_id, model, plate FROM scooters WHERE id = ?',
        [$id]
    );

    if ($row === false) {
        return null;
    }

    return new Scooter(
        ownerId: (int) $row['owner_id'],
        model: $row['model'],
        plate: $row['plate'],
        id: (int) $row['id']
    );
}

public function grantAccess(int $scooterId, int $userId): void
{
    $this->connection->insert('scooter_access', [
        'scooter_id' => $scooterId,
        'user_id'    => $userId,
    ]);
}

public function hasAccess(int $scooterId, int $userId): bool
{
    return $this->connection->fetchOne(
        'SELECT 1 FROM scooter_access WHERE scooter_id = ? AND user_id = ?',
        [$scooterId, $userId]
    ) !== false;
}

public function findByOwnerId(int $ownerId): array
{
    $rows = $this->connection->fetchAllAssociative(
        'SELECT id, owner_id, model, plate FROM scooters WHERE owner_id = ? ORDER BY id DESC',
        [$ownerId]
    );

    return array_map(
        fn (array $row) => new Scooter(
            ownerId: (int) $row['owner_id'],
            model: $row['model'],
            plate: $row['plate'],
            id: (int) $row['id']
        ),
        $rows
    );
}
}