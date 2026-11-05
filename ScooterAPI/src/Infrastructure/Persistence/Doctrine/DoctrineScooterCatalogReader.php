<?php

namespace Src\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Src\Application\Scooter\Query\GetAvailableScooters\AvailableScooterView;
use Src\Application\Scooter\Query\GetAvailableScooters\ScooterCatalogReaderInterface;
use Src\Domain\Reservation\Entity\Reservation;

class DoctrineScooterCatalogReader implements ScooterCatalogReaderInterface
{
    public function __construct(private Connection $connection) {}

    public function findAvailableFor(int $userId): array
    {
        $rows = $this->connection->fetchAllAssociative(
           'SELECT s.id, s.model, s.plate, u.name AS owner_name, u.email AS owner_email,

                CASE WHEN EXISTS (
                    SELECT 1 FROM reservations r
                    WHERE r.scooter_id = s.id
                      AND r.status = :accepted
                      AND r.start_at <= :now
                      AND r.end_at > :now
                ) THEN 0 ELSE 1 END AS available_now,

                CASE WHEN EXISTS (
                    SELECT 1 FROM scooter_access a
                    WHERE a.scooter_id = s.id
                      AND a.user_id = :userId
                ) THEN 1 ELSE 0 END AS authorized

             FROM scooters s
             JOIN users u ON u.id = s.owner_id

             ORDER BY s.id DESC',

            [
                'accepted' => Reservation::STATUS_ACCEPTED,
                'now'      => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'userId'   => $userId,
            ]
        );

        return array_map(fn (array $row) => new AvailableScooterView(
            id: (int) $row['id'],
            model: $row['model'],
            plate: $row['plate'],
            ownerName: $row['owner_name'],
            ownerEmail: $row['owner_email'],
            availableNow: (bool) (int) $row['available_now'],
            authorized: (bool) (int) $row['authorized']
        ), $rows);
    }
}