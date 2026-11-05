<?php

namespace Src\Domain\Reservation\Exception;

final class ReservationAlreadyProcessedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Cette demande a déjà été traitée.");
    }
}