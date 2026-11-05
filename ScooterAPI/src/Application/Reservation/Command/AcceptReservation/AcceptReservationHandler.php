<?php

namespace Src\Application\Reservation\Command\AcceptReservation;

use Src\Domain\Reservation\Exception\ReservationNotFoundException;
use Src\Domain\Reservation\Exception\ScooterNotAvailableException;
use Src\Domain\Reservation\Repository\ReservationRepositoryInterface;
use Src\Domain\Scooter\Exception\NotScooterOwnerException;
use Src\Domain\Scooter\Exception\ScooterNotFoundException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class AcceptReservationHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function __invoke(AcceptReservationCommand $command): void
    {
        // 1. La demande existe ?
        $reservation = $this->reservationRepository->findById($command->reservationId);
        if ($reservation === null) {
            throw new ReservationNotFoundException();
        }

        // 2. Le scooter concerné appartient bien à l'utilisateur connecté ?
        $scooter = $this->scooterRepository->findById($reservation->getScooterId());
        if ($scooter === null) {
            throw new ScooterNotFoundException();
        }
        if (!$scooter->isOwnedBy($command->ownerId)) {
            throw new NotScooterOwnerException();
        }

        // 3. Elle est encore en attente ? (sinon ReservationAlreadyProcessedException)
        $reservation->accept();

        // 4. Le créneau est toujours libre ? (une autre demande a pu être acceptée entre-temps)
        if ($this->reservationRepository->reservationAlreadyTake(
            $scooter->getId(),
            $reservation->getStartAt(),
            $reservation->getEndAt()
        )) {
            throw new ScooterNotAvailableException();
        }

        // 5. On enregistre le nouveau statut
        $this->reservationRepository->update($reservation);
    }
}