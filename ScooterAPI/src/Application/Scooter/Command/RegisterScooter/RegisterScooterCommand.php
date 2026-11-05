<?php

namespace Src\Application\Scooter\Command\RegisterScooter;

final readonly class RegisterScooterCommand
{
    public function __construct(
        public int $ownerId,
        public string $model,
        public string $plate
    ) {}
}