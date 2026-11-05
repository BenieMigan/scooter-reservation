<?php

namespace Src\Domain\User\Exception;

final class UnknownUserException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Aucun utilisateur avec cette adresse email.");
    }
}