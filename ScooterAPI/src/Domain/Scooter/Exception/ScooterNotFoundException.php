<?php

namespace Src\Domain\Scooter\Exception;

final class ScooterNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Scooter introuvable.');
    }
}