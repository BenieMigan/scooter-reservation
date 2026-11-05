<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Src\Infrastructure\ApiPlatform\State\Processor\RegisterScooterProcessor;
use Src\Infrastructure\ApiPlatform\State\Provider\MyScootersProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Scooter',
    operations: [
        new GetCollection(
            uriTemplate: '/scooters',
            provider: MyScootersProvider::class,
            security: "is_granted('ROLE_OWNER')"
        ),
        new Post(
            uriTemplate: '/scooters',
            status: 201,
            processor: RegisterScooterProcessor::class,
            security: "is_granted('ROLE_OWNER')",
            output: false // ← Empêche la génération de l'IRI
        ),
    ]
)]
class ScooterResource
{
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[Assert\NotBlank(message: "Le modèle est obligatoire.")]
    public string $model = '';

    #[Assert\NotBlank(message: "La plaque est obligatoire.")]
    public string $plate = '';
}