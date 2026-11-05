<?php

namespace Src\Domain\User\Exception;

final class EmailAlreadyUsedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Cette adresse email est déjà utilisée.');
    }
}