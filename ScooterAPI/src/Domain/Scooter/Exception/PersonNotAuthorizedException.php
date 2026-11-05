<?php

namespace Src\Domain\Scooter\Exception;

final class PersonNotAuthorizedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Cette personne n’a pas été autorisée à utiliser ce scooter.");
    }
}