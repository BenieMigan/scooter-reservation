<?php

namespace Src\Application\Scooter\Query\GetAvailableScooters;

final readonly class AvailableScooterView
{
public function __construct(
    public int $id,
    public string $model,
    public string $plate,
    public string $ownerName,
    public string $ownerEmail,
    public bool $availableNow,
    public bool $authorized
) {}
}