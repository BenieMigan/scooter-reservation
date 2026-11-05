<?php
namespace Src\Application\Scooter\Query\GetAvailableScooters;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetAvailableScootersHandler
{
    public function __construct(private ScooterCatalogReaderInterface $reader) {}

    public function __invoke(GetAvailableScootersQuery $query): array
    {
        return $this->reader->findAvailableFor($query->userId);
    }
}