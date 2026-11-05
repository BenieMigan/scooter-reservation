<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Src\Infrastructure\ApiPlatform\State\Processor\GrantScooterAccessProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'ScooterAccess',
    operations: [
        new Post(
            uriTemplate: '/scooter-access',
            status: 201,
            processor: GrantScooterAccessProcessor::class,
            security: "is_granted('ROLE_OWNER')",
            output: false // ← Empêche la génération de l'IRI

        )
    ]
)]
class ScooterAccessResource
{
    public ?int $id = null;

    #[Assert\Positive(message: "L'identifiant du scooter est obligatoire.")]
    public int $scooterId = 0;


    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    public string $email = '';
}