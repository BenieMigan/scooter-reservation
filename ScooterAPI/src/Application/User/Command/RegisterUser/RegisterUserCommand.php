<?php

// Informations pour une demande d'enregistrement

namespace Src\Application\User\Command\RegisterUser;

final readonly class RegisterUserCommand{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) 
    {
    }

}