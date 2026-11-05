<?php

namespace Src\Application\Reservation\Query\GetMyReservations;

use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestReaderInterface;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestView;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetMyReservationsHandler
{
    public function __construct(
        private ReservationRequestReaderInterface $reader
    ) {}

    /** @return ReservationRequestView[] */
    public function __invoke(GetMyReservationsQuery $query): array
    {
        return $this->reader->findByRequesterId($query->requesterId);
    }
}