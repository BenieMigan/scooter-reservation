<?php

namespace Src\Infrastructure\Security;

use Src\Application\User\Contract\PasswordHasherInterface;

class NativePasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
{
    return password_verify($plainPassword, $hashedPassword);
}
}
