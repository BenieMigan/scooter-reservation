<?php

namespace Src\Application\Reservation\Query\GetReceivedRequests;

final readonly class ReservationRequestView
{
    public function __construct(
        public int $id,
        public int $scooterId,
        public string $plate,
        public string $requesterName,
        public string $requesterEmail,
        public \DateTimeImmutable $startAt,
        public \DateTimeImmutable $endAt,
        public string $status,
        public ?string $refusalReason = null

    ) {}
}