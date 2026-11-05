<?php

namespace Src\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestReaderInterface;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestView;
use Src\Domain\Reservation\Entity\Reservation;

class DoctrineReservationRequestReader implements ReservationRequestReaderInterface
{
    private const SELECT = 'SELECT r.id, r.scooter_id, s.plate,
                    u.name AS requester_name, u.email AS requester_email,
                    r.start_at, r.end_at, r.status, r.refusal_reason
            FROM reservations r
            JOIN scooters s ON s.id = r.scooter_id
            JOIN users u ON u.id = r.user_id';

    public function __construct(
        private Connection $connection
    ) {}

    public function findPendingByOwnerId(int $ownerId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            self::SELECT . ' WHERE s.owner_id = ? AND r.status = ? ORDER BY r.start_at ASC',
            [$ownerId, Reservation::STATUS_PENDING]
        );

        return array_map(fn (array $row) => $this->toView($row), $rows);
    }

    public function findAllByOwnerId(int $ownerId): array
    {
        $rows = $this->connection->fetchAllAssociative(
            self::SELECT . ' WHERE s.owner_id = ? ORDER BY r.start_at DESC',
            [$ownerId]
        );

        return array_map(fn (array $row) => $this->toView($row), $rows);
    }

    private function toView(array $row): ReservationRequestView
    {
        return new ReservationRequestView(
            id: (int) $row['id'],
            scooterId: (int) $row['scooter_id'],
            plate: $row['plate'],
            requesterName: $row['requester_name'],
            requesterEmail: $row['requester_email'],
            startAt: new \DateTimeImmutable($row['start_at']),
            endAt: new \DateTimeImmutable($row['end_at']),
            status: $row['status'],
            refusalReason: $row['refusal_reason']
        );
    }

    public function findByRequesterId(int $requesterId): array
{
    $rows = $this->connection->fetchAllAssociative(
        self::SELECT . ' WHERE r.user_id = ? ORDER BY r.start_at DESC',
        [$requesterId]
    );

    return array_map(fn (array $row) => $this->toView($row), $rows);
}
    
}