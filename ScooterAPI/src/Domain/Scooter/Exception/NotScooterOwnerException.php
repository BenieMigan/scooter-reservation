<?php

namespace Src\Domain\Scooter\Exception;

final class NotScooterOwnerException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Seul le propriétaire du scooter peut effectuer cette action.");
    }
}