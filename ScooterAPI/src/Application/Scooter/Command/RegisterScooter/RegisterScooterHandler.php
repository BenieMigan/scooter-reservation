<?php

namespace Src\Application\Scooter\Command\RegisterScooter;

use Src\Domain\Scooter\Entity\Scooter;
use Src\Domain\Scooter\Exception\ScooterAlreadyExistsException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterScooterHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository
    ) {}

    public function __invoke(RegisterScooterCommand $command): void
    {
        // On normalise la plaque : "ab 1234 " et "AB 1234" sont la même plaque
        $plate = strtoupper(trim($command->plate));

        if ($this->scooterRepository->existsByPlate($plate)) {
            throw new ScooterAlreadyExistsException();
        }

        $scooter = new Scooter(
            ownerId: $command->ownerId,
            model: trim($command->model),
            plate: $plate
        );

        $this->scooterRepository->save($scooter);
    }
}