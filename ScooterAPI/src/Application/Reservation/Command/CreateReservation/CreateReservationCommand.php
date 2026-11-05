<?php

namespace Src\Application\Reservation\Command\CreateReservation;

final readonly class CreateReservationCommand
{
    public function __construct(
        public int $requesterId,            // l'utilisateur connecté (le propriétaire)
        public int $scooterId,
        public string $email,               // la personne pour qui on réserve
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt
    ) {}
}