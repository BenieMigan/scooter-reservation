<?php

namespace Src\Infrastructure\ApiPlatform\Resource;

use Symfony\Component\Validator\Constraints as Assert;

// Ce que le propriétaire envoie quand il refuse
class RefuseReservationInput
{
    #[Assert\NotBlank(message: "Le motif du refus est obligatoire.")]
    public string $reason = '';
}