<?php

namespace Src\Domain\Reservation\Exception;

final class InvalidReservationPeriodException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("L'heure de fin doit être après l'heure de début.");
    }
}