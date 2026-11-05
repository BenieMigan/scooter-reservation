<?php

namespace Src\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Src\Application\Scooter\Query\GetMyScooters\GetMyScootersQuery;
use Src\Domain\Scooter\Entity\Scooter;
use Src\Infrastructure\ApiPlatform\Resource\ScooterResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProviderInterface<ScooterResource>
 */
class MyScootersProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private Security $security
    ) {
        $this->messageBus = $queryBus;
    }

    /** @return ScooterResource[] */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        // handle() envoie la Query ET récupère la réponse du Handler
        $scooters = $this->handle(new GetMyScootersQuery($user->getId()));

        return array_map(function (Scooter $scooter) {
            $resource = new ScooterResource();
            $resource->id = $scooter->getId();
            $resource->model = $scooter->getModel();
            $resource->plate = $scooter->getPlate();

            return $resource;
        }, $scooters);
    }
}