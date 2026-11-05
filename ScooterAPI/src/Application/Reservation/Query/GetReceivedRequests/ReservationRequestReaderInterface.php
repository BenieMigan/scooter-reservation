<?php

namespace Src\Application\Reservation\Query\GetReceivedRequests;

interface ReservationRequestReaderInterface
{
    /** @return ReservationRequestView[] */
    public function findPendingByOwnerId(int $ownerId): array;

    // Trouve-moi toutes les réservations des scooters de ce propriétaire

    /** @return ReservationRequestView[] */
    public function findAllByOwnerId(int $ownerId): array;

     /** @return ReservationRequestView[] */
    public function findByRequesterId(int $requesterId): array;

}