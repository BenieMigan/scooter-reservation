<?php

namespace Src\Application\User\Contract;

use Src\Domain\User\Entity\User;

interface TokenGeneratorInterface
{
    public function generate(User $user): string;
}