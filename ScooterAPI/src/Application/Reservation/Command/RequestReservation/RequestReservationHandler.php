<?php

namespace Src\Application\Reservation\Command\RequestReservation;

use Src\Domain\Reservation\Entity\Reservation;
use Src\Domain\Reservation\Exception\OwnerCannotRequestOwnScooterException;
use Src\Domain\Reservation\Exception\ReservationInThePastException;
use Src\Domain\Reservation\Exception\ScooterNotAvailableException;
use Src\Domain\Reservation\Repository\ReservationRepositoryInterface;
use Src\Domain\Scooter\Exception\ScooterNotFoundException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class RequestReservationHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function __invoke(RequestReservationCommand $command): void
    {
        // 1. Le scooter existe ?
        $scooter = $this->scooterRepository->findById($command->scooterId);
        if ($scooter === null) {
            throw new ScooterNotFoundException();
        }

        // 2. Le propriétaire ne fait pas de demande pour son propre scooter
        if ($scooter->isOwnedBy($command->requesterId)) {
            throw new OwnerCannotRequestOwnScooterException();
        }

        // 3. Le créneau commence dans le futur
        if ($command->startAt <= new \DateTimeImmutable()) {
            throw new ReservationInThePastException();
        }

        // 4. On crée la demande : statut EN ATTENTE
        //    (la fin après le début est vérifiée par Reservation elle-même)
        $reservation = new Reservation(
            scooterId: $scooter->getId(),
            userId: $command->requesterId,
            startAt: $command->startAt,
            endAt: $command->endAt,
            status: Reservation::STATUS_PENDING
        );

        // 5. Inutile de demander un créneau déjà accepté pour quelqu'un d'autre
        if ($this->reservationRepository->reservationAlreadyTake(
            $scooter->getId(),
            $command->startAt,
            $command->endAt
        )) {
            throw new ScooterNotAvailableException();
        }

        $this->reservationRepository->save($reservation);
    }
}