<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Src\Infrastructure\ApiPlatform\State\Processor\CreateReservationProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Reservation',
    operations: [
        new Post(
            uriTemplate: '/reservations',
            status: 201,
            processor: CreateReservationProcessor::class,
            security: "is_granted('ROLE_OWNER')",
            output: false // ← Empêche la génération de l'IRI

        )
    ]
)]
class ReservationResource
{
    public ?int $id = null;

    #[Assert\Positive(message: "L'identifiant du scooter est obligatoire.")]
    public int $scooterId = 0;

    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    public string $email = '';

    #[Assert\NotNull(message: "La date et l'heure de début sont obligatoires.")]
    public ?\DateTimeImmutable $startAt = null;

    #[Assert\NotNull(message: "La date et l'heure de fin sont obligatoires.")]
    public ?\DateTimeImmutable $endAt = null;
}