<?php

namespace Src\Application\Reservation\Query\GetReservationHistory;

final readonly class GetReservationHistoryQuery
{
    public function __construct(
        public int $ownerId
    ) {}
}