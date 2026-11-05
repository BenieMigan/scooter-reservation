<?php

namespace Src\Domain\User\Exception;

final class InvalidCredentialsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Email ou mot de passe incorrect.');
    }
}