<?php

namespace Src\Application\Scooter\Query\GetMyScooters;

use Src\Domain\Scooter\Entity\Scooter;
use Src\Domain\Scooter\Repository\ScooterRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetMyScootersHandler
{
    public function __construct(
        private ScooterRepositoryInterface $scooterRepository
    ) {}

    /** @return Scooter[] */
    public function __invoke(GetMyScootersQuery $query): array
    {
        return $this->scooterRepository->findByOwnerId($query->ownerId);
    }
}