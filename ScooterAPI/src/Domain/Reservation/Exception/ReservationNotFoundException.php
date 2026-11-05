<?php

namespace Src\Domain\Reservation\Exception;

final class ReservationNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Réservation introuvable");
    }
}