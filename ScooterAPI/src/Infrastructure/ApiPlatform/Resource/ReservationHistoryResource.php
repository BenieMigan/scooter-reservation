<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Src\Infrastructure\ApiPlatform\State\Provider\ReservationHistoryProvider;

#[ApiResource(
    shortName: 'ReservationHistory',
    operations: [
        new GetCollection(
            uriTemplate: '/reservation-history',
            security: "is_granted('ROLE_OWNER')",
            provider: ReservationHistoryProvider::class
        ),
    ]
)]
class ReservationHistoryResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    public ?int $scooterId = null;
    public ?string $plate = null;
    public ?string $requesterName = null;
    public ?string $requesterEmail = null;
    public ?\DateTimeImmutable $startAt = null;
    public ?\DateTimeImmutable $endAt = null;
    public ?string $status = null;
    public ?string $refusalReason = null;
}