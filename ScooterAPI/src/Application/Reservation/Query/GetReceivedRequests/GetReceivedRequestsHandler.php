<?php

namespace Src\Application\Reservation\Query\GetReceivedRequests;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetReceivedRequestsHandler
{
    public function __construct(
        private ReservationRequestReaderInterface $reader
    ) {}

    /** @return ReservationRequestView[] */
    public function __invoke(GetReceivedRequestsQuery $query): array
    {
        return $this->reader->findPendingByOwnerId($query->ownerId);
    }
}