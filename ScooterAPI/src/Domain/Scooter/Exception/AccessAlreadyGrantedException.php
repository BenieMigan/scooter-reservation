<?php

namespace Src\Domain\Scooter\Exception;

final class AccessAlreadyGrantedException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Cette personne a déjà accès à ce scooter.');
    }
}