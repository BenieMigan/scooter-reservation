<?php

namespace Src\Application\Scooter\Command\GrantScooterAccess;

use Src\Domain\Scooter\Exception\AccessAlreadyGrantedException;
use Src\Domain\Scooter\Exception\NotScooterOwnerException;
use Src\Domain\Scooter\Exception\ScooterNotFoundException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Src\Domain\User\Exception\UnknownUserException;
use Src\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class GrantScooterAccessHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(GrantScooterAccessCommand $command): void
    {
        // Le scooter existe ?
        $scooter = $this->scooterRepository->findById($command->scooterId);
        if ($scooter === null) {
            throw new ScooterNotFoundException();
        }

        // Seul le propriétaire peut autoriser quelqu'un
        if (!$scooter->isOwnedBy($command->requesterId)) {
            throw new NotScooterOwnerException();
        }

        // La personne à autoriser existe ?
        $email = strtolower(trim($command->email));

        $person = $this->userRepository->findByEmail($email);
        if ($person === null) {
            throw new UnknownUserException();
        }

        // Elle n'a pas déjà accès (le propriétaire a déjà accès de fait)
        if ($scooter->isOwnedBy($person->getId())
            || $this->scooterRepository->hasAccess($scooter->getId(), $person->getId())) {
            throw new AccessAlreadyGrantedException();
        }

        // On enregistre l'autorisation
        $this->scooterRepository->grantAccess($scooter->getId(), $person->getId());
    }
}