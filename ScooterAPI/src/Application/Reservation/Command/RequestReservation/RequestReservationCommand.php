<?php

namespace Src\Application\Reservation\Command\RequestReservation;

final readonly class RequestReservationCommand
{
    public function __construct(
        public int $requesterId,   // l'utilisateur connecté (celui qui demande)
        public int $scooterId,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt
    ) {}
}