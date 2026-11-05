<?php

namespace Src\Domain\Reservation\Exception;

final class ScooterNotAvailableException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Ce scooter est déjà réservé sur ce créneau.");
    }
}