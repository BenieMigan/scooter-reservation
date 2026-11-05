<?php

namespace Src\Application\Reservation\Command\RefuseReservation;

use Src\Domain\Reservation\Exception\ReservationNotFoundException;
use Src\Domain\Reservation\Repository\ReservationRepositoryInterface;
use Src\Domain\Scooter\Exception\NotScooterOwnerException;
use Src\Domain\Scooter\Exception\ScooterNotFoundException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class RefuseReservationHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function __invoke(RefuseReservationCommand $command): void
    {
        $reservation = $this->reservationRepository->findById($command->reservationId);
        if ($reservation === null) {
            throw new ReservationNotFoundException();
        }

        $scooter = $this->scooterRepository->findById($reservation->getScooterId());
        if ($scooter === null) {
            throw new ScooterNotFoundException();
        }
        if (!$scooter->isOwnedBy($command->ownerId)) {
            throw new NotScooterOwnerException();
        }

        // Passe en REFUSED et garde le motif (ReservationAlreadyProcessedException si déjà traitée)
        $reservation->refuse(trim($command->reason));

        $this->reservationRepository->update($reservation);
    }
}