<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Src\Infrastructure\ApiPlatform\State\Processor\AcceptReservationProcessor;
use Src\Infrastructure\ApiPlatform\State\Processor\RefuseReservationProcessor;
use Src\Infrastructure\ApiPlatform\State\Processor\RequestReservationProcessor;
use Src\Infrastructure\ApiPlatform\State\Provider\ReceivedRequestsProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'ReservationRequest',
    operations: [
        // Le propriétaire voit les demandes reçues
        new GetCollection(
            uriTemplate: '/reservation-requests',
            security: "is_granted('ROLE_OWNER')",
            provider: ReceivedRequestsProvider::class
        ),
        // Quelqu'un fait une demande
        new Post(
            uriTemplate: '/reservation-requests',
            status: 201,
            processor: RequestReservationProcessor::class
        ),
        // Le propriétaire accepte (pas de corps JSON)
        new Post(
            uriTemplate: '/reservation-requests/{id}/accept',
            uriVariables: ['id'],
            read: false,
            input: false,
            output: false,
            status: 204,
            processor: AcceptReservationProcessor::class,
            security: "is_granted('ROLE_OWNER')"

        ),
        // Le propriétaire refuse (corps JSON : {"reason": "..."})
        new Post(
            uriTemplate: '/reservation-requests/{id}/refuse',
            uriVariables: ['id'],
            read: false,
            input: RefuseReservationInput::class,
            output: false,
            status: 204,
            processor: RefuseReservationProcessor::class,
            security: "is_granted('ROLE_OWNER')"

        ),
    ]
)]
class ReservationRequestResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    // --- Ce que le client envoie pour faire une demande ---
    #[Assert\Positive(message: "L'identifiant du scooter est obligatoire.")]
    public int $scooterId = 0;

    #[Assert\NotNull(message: "La date et l'heure de début sont obligatoires.")]
    public ?\DateTimeImmutable $startAt = null;

    #[Assert\NotNull(message: "La date et l'heure de fin sont obligatoires.")]
    public ?\DateTimeImmutable $endAt = null;

    // --- Ce que le propriétaire voit dans sa liste (lecture seule) ---
    #[ApiProperty(writable: false)]
    public ?string $plate = null;

    #[ApiProperty(writable: false)]
    public ?string $requesterName = null;

    #[ApiProperty(writable: false)]
    public ?string $requesterEmail = null;

    #[ApiProperty(writable: false)]
    public ?string $status = null;
}