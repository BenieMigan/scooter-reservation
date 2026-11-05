<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Src\Infrastructure\ApiPlatform\State\Provider\MyReservationsProvider;

#[ApiResource(
    shortName: 'MyReservation',
    operations: [
        new GetCollection(
            uriTemplate: '/my-reservations',
            provider: MyReservationsProvider::class
        ),
    ]
)]
class MyReservationResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    public ?int $scooterId = null;
    public ?string $plate = null;
    public ?\DateTimeImmutable $startAt = null;
    public ?\DateTimeImmutable $endAt = null;
    public ?string $status = null;          // PENDING, ACCEPTED ou REFUSED
    public ?string $refusalReason = null;   // rempli seulement si REFUSED
}