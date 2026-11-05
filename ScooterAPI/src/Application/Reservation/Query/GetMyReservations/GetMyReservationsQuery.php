<?php

namespace Src\Application\Reservation\Query\GetMyReservations;

final readonly class GetMyReservationsQuery
{
    public function __construct(
        public int $requesterId   // l'utilisateur connecté
    ) {}
}