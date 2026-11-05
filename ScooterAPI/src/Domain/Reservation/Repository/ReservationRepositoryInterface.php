<?php

namespace Src\Domain\Reservation\Repository;

use Src\Domain\Reservation\Entity\Reservation;

interface ReservationRepositoryInterface
{
    public function save(Reservation $reservation): void;

    // Y a-t-il déjà une réservation ACCEPTÉE a ce créneau ?
    public function reservationAlreadyTake(
        int $scooterId,
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt
    ): bool;
         public function update(Reservation $reservation): void;    // met à jour le statut

        public function findById(int $id): ?Reservation;

}

