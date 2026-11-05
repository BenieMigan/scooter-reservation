<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Src\Infrastructure\ApiPlatform\State\Provider\AvailableScootersProvider;

#[ApiResource(
    shortName: 'AvailableScooter',
    operations: [
        new GetCollection(
            uriTemplate: '/available-scooters',
            provider: AvailableScootersProvider::class
        ),
    ]
)]
class AvailableScooterResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    public ?string $model = null;

    public ?string $plate = null;

    public ?string $ownerName = null;

    public ?string $ownerEmail = null;

    public bool $availableNow = true;

    public bool $authorized = false;
}