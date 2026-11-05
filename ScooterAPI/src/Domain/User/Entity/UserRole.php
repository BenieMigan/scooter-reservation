<?php

namespace Src\Domain\User\Entity;

enum UserRole: string
{
    case Owner = 'ROLE_OWNER';
    case User  = 'ROLE_USER';
}