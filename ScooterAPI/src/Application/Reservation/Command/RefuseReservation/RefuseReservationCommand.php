<?php

namespace Src\Application\Reservation\Command\RefuseReservation;

final readonly class RefuseReservationCommand
{
    public function __construct(
        public int $ownerId,
        public int $reservationId,
        public string $reason
    ) {}
}