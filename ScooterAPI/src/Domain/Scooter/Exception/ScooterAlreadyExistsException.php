<?php

namespace Src\Domain\Scooter\Exception;

final class ScooterAlreadyExistsException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Un scooter avec cette plaque existe déjà.');
    }
}