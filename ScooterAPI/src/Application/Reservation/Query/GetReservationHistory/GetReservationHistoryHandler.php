<?php

namespace Src\Application\Reservation\Query\GetReservationHistory;

use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestReaderInterface;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestView;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetReservationHistoryHandler
{
    public function __construct(
        private ReservationRequestReaderInterface $reader
    ) {}

    /** @return ReservationRequestView[] */
    public function __invoke(GetReservationHistoryQuery $query): array
    {
        return $this->reader->findAllByOwnerId($query->ownerId);
    }
}