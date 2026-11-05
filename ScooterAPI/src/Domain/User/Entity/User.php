<?php

namespace Src\Domain\User\Entity;

class User
{
    public function __construct(
        private string $name,
        private string $email,
        private string $password,
        private UserRole $role = UserRole::User,
        private ?int $id = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): UserRole { return $this->role; }
}