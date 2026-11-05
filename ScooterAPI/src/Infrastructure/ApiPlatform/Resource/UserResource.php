<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Src\Infrastructure\ApiPlatform\State\Processor\RegisterUserProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Post(
            uriTemplate: '/auth/register',
            status: 201,
            processor: RegisterUserProcessor::class,
            output: false
        )
    ]
)]
class UserResource
{
    public ?int $id = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    public string $name = '';

    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    public string $email = '';

    #[ApiProperty(readable: false)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit faire au moins 6 caractères.")]
    public string $password = '';
}