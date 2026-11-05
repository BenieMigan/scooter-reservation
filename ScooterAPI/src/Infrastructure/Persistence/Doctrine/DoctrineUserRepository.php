<?php

namespace Src\Infrastructure\Persistence\Doctrine;

use Src\Domain\User\Entity\User;
use Src\Domain\User\Entity\UserRole;
use Src\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Connection;

class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection
    ) {}

    public function save(User $user): void
    {
        $this->connection->insert('users', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()->value,
        ]);
    }

    public function existsByEmail(string $email): bool
    {
        $result = $this->connection->fetchOne(
            'SELECT id FROM users WHERE email = ?',
            [$email]
        );
        return $result !== false;
    }

    public function findByEmail(string $email): ?User
    {
        $row = $this->connection->fetchAssociative(
            'SELECT id, name, email, password, role FROM users WHERE email = ?',
            [$email]
        );

        if ($row === false) {
            return null;
        }

        return new User(
            name: $row['name'],
            email: $row['email'],
            password: $row['password'],
            role: UserRole::from($row['role']),
            id: (int) $row['id']
        );
    }
}