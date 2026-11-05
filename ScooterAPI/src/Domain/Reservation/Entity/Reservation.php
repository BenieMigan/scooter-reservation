<?php

namespace Src\Domain\Reservation\Entity;

use Src\Domain\Reservation\Exception\InvalidReservationPeriodException;
use Src\Domain\Reservation\Exception\ReservationAlreadyProcessedException;

class Reservation
{
    public const STATUS_PENDING  = 'PENDING';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REFUSED  = 'REFUSED';

    public function __construct(
        private int $scooterId,
        private int $userId,
        private \DateTimeImmutable $startAt,
        private \DateTimeImmutable $endAt,
        private string $status,
        private ?string $refusalReason = null,
        private ?int $id = null
    ) {
        if ($endAt <= $startAt) {
            throw new InvalidReservationPeriodException();
        }
    }

    public function accept(): void
    {
        $this->ensurePending();
        $this->status = self::STATUS_ACCEPTED;
    }

    public function refuse(string $reason): void
    {
        $this->ensurePending();
        $this->status = self::STATUS_REFUSED;
        $this->refusalReason = $reason;
    }

    private function ensurePending(): void
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new ReservationAlreadyProcessedException();
        }
    }

    public function getId(): ?int { return $this->id; }
    public function getScooterId(): int { return $this->scooterId; }
    public function getUserId(): int { return $this->userId; }
    public function getStartAt(): \DateTimeImmutable { return $this->startAt; }
    public function getEndAt(): \DateTimeImmutable { return $this->endAt; }
    public function getStatus(): string { return $this->status; }
    public function getRefusalReason(): ?string { return $this->refusalReason; }
}