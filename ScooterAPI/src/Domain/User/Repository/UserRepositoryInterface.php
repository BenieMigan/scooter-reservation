<?php

namespace Src\Domain\User\Repository;

use Src\Domain\User\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function existsByEmail(string $email): bool;

    public function findByEmail(string $email): ?User;
}