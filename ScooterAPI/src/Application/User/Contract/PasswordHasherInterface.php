<?php

namespace Src\Application\User\Contract;

interface PasswordHasherInterface
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, string $hashedPassword): bool;
}