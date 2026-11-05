<?php

namespace Src\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Src\Application\Scooter\Query\GetAvailableScooters\AvailableScooterView;
use Src\Application\Scooter\Query\GetAvailableScooters\GetAvailableScootersQuery;
use Src\Infrastructure\ApiPlatform\Resource\AvailableScooterResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class AvailableScootersProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus, private Security $security)
    {
        $this->messageBus = $queryBus;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        $views = $this->handle(new GetAvailableScootersQuery($user->getId()));

     return array_map(function (AvailableScooterView $v) {
    $r = new AvailableScooterResource();

    $r->id = $v->id;
    $r->model = $v->model;
    $r->plate = $v->plate;
    $r->ownerName = $v->ownerName;
    $r->ownerEmail = $v->ownerEmail;
    $r->availableNow = $v->availableNow;
    $r->authorized = $v->authorized;

    return $r;
}, $views);
    }
}