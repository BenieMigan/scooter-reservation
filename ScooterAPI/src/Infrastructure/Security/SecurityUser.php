<?php

namespace Src\Infrastructure\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private int $id,
        private string $email,
        private string $hashedPassword,
        private string $role
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    // L'identifiant de connexion : ici, l'email
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    // Le mot de passe HACHÉ (celui de la base)
    public function getPassword(): string
    {
        return $this->hashedPassword;
    }

    public function eraseCredentials(): void
    {
    }
}