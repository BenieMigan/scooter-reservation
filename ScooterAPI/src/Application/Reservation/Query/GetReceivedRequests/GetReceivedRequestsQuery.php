<?php

namespace Src\Application\Reservation\Query\GetReceivedRequests;

final readonly class GetReceivedRequestsQuery
{
    public function __construct(
        public int $ownerId
    ) {}
}