<?php

namespace Src\Domain\Reservation\Exception;

final class OwnerCannotRequestOwnScooterException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Vous êtes propriétaire de ce scooter : réservez directement.");
    }
}