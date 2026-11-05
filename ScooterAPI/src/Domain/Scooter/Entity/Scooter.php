<?php

namespace Src\Domain\Scooter\Entity;

class Scooter
{
    public function __construct(
        private int $ownerId,
        private string $model,
        private string $plate,
        private ?int $id = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getOwnerId(): int { return $this->ownerId; }
    public function getModel(): string { return $this->model; }
    public function getPlate(): string { return $this->plate; }

    public function isOwnedBy(int $userId): bool
    {
        return $this->ownerId === $userId;
    }
}