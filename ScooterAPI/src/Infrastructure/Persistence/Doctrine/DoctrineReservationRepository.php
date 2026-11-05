<?php

namespace Src\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Src\Domain\Reservation\Entity\Reservation;
use Src\Domain\Reservation\Repository\ReservationRepositoryInterface;

class DoctrineReservationRepository implements ReservationRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function save(Reservation $reservation): void
    {
        $this->connection->insert('reservations', [
            'scooter_id'     => $reservation->getScooterId(),
            'user_id'        => $reservation->getUserId(),
            'start_at'       => $this->toDatabase($reservation->getStartAt()),
            'end_at'         => $this->toDatabase($reservation->getEndAt()),
            'status'         => $reservation->getStatus(),
            'refusal_reason' => $reservation->getRefusalReason(),
        ]);
    }

    public function reservationAlreadyTake(
        int $scooterId,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt
    ): bool {
        return $this->connection->fetchOne(
            'SELECT 1 FROM reservations
            WHERE scooter_id = ? AND status = ? AND start_at < ? AND end_at > ?',
            [
                $scooterId,
                Reservation::STATUS_ACCEPTED,
                $this->toDatabase($endAt),
                $this->toDatabase($startAt),
            ]
        ) !== false;
    }

    // On met toutes les dates dans le fuseau horaire du serveur avant de les écrire
    private function toDatabase(\DateTimeImmutable $date): string
    {
        return $date
            ->setTimezone(new \DateTimeZone(date_default_timezone_get()))
            ->format('Y-m-d H:i:s');
    }

    public function findById(int $id): ?Reservation
{
    $row = $this->connection->fetchAssociative(
        'SELECT id, scooter_id, user_id, start_at, end_at, status, refusal_reason
        FROM reservations WHERE id = ?',
        [$id]
    );

    if ($row === false) {
        return null;
    }

    return new Reservation(
        scooterId: (int) $row['scooter_id'],
        userId: (int) $row['user_id'],
        startAt: new \DateTimeImmutable($row['start_at']),
        endAt: new \DateTimeImmutable($row['end_at']),
        status: $row['status'],
        refusalReason: $row['refusal_reason'],
        id: (int) $row['id']
    );
}

public function update(Reservation $reservation): void
{
    $this->connection->update(
        'reservations',
        [
            'status'         => $reservation->getStatus(),
            'refusal_reason' => $reservation->getRefusalReason(),
        ],
        ['id' => $reservation->getId()]
    );
}
}