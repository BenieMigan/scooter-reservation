<?php

namespace Src\Domain\Reservation\Exception;

final class ReservationInThePastException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("La réservation doit commencer dans le futur.");
    }
}