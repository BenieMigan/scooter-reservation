<?php

namespace Src\Application\Reservation\Command\AcceptReservation;

final readonly class AcceptReservationCommand
{
    public function __construct(
        public int $ownerId,        // l'utilisateur connecté
        public int $reservationId
    ) {}
}