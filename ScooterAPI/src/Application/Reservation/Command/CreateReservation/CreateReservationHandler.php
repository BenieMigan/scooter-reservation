<?php

namespace Src\Application\Reservation\Command\CreateReservation;

use Src\Domain\Reservation\Entity\Reservation;
use Src\Domain\Reservation\Exception\ReservationInThePastException;
use Src\Domain\Reservation\Exception\ScooterNotAvailableException;
use Src\Domain\Reservation\Repository\ReservationRepositoryInterface;
use Src\Domain\Scooter\Exception\NotScooterOwnerException;
use Src\Domain\Scooter\Exception\PersonNotAuthorizedException;
use Src\Domain\Scooter\Exception\ScooterNotFoundException;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Src\Domain\User\Exception\UnknownUserException;
use Src\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateReservationHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository,
        private UserRepositoryInterface $userRepository,
        private ReservationRepositoryInterface $reservationRepository
    ) {}

    public function __invoke(CreateReservationCommand $command): void
    {
        // 1. Le scooter existe ?
        $scooter = $this->scooterRepository->findById($command->scooterId);
        if ($scooter === null) {
            throw new ScooterNotFoundException();
        }

        // 2. Seul le propriétaire peut réserver pour quelqu'un
        if (!$scooter->isOwnedBy($command->requesterId)) {
            throw new NotScooterOwnerException();
        }

        // 3. La personne a un compte ?
        $person = $this->userRepository->findByEmail($command->email);
        if ($person === null) {
            throw new UnknownUserException();
        }

        // 4. Elle a été autorisée au préalable ?
        if (!$this->scooterRepository->hasAccess($scooter->getId(), $person->getId())) {
            throw new PersonNotAuthorizedException();
        }

        // 5. Le créneau est dans le futur et cohérent
        //    (la fin après le début est vérifiée par Reservation elle-même)
        if ($command->startAt <= new \DateTimeImmutable()) {
            throw new ReservationInThePastException();
        }

        $reservation = new Reservation(
            scooterId: $scooter->getId(),
            userId: $person->getId(),
            startAt: $command->startAt,
            endAt: $command->endAt,
            status: Reservation::STATUS_ACCEPTED  // le propriétaire décide, donc c'est accepté d'office
        );

        // 6. Le scooter est libre sur ce créneau ?
        if ($this->reservationRepository->reservationAlreadyTake(
            $scooter->getId(),
            $command->startAt,
            $command->endAt
        )) {
            throw new ScooterNotAvailableException();
        }

        // 7. On enregistre
        $this->reservationRepository->save($reservation);
    }
}