<?php

namespace Src\Application\Scooter\Command\GrantScooterAccess;

final readonly class GrantScooterAccessCommand
{
    public function __construct(
        public int $requesterId,   // celui qui fait la demande (utilisateur connecté)
        public int $scooterId,
        public string $email       // la personne à autoriser
    ) {}
}