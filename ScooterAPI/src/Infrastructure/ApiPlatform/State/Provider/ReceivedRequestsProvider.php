<?php

namespace Src\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Src\Application\Reservation\Query\GetReceivedRequests\GetReceivedRequestsQuery;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestView;
use Src\Infrastructure\ApiPlatform\Resource\ReservationRequestResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProviderInterface<ReservationRequestResource>
 */
class ReceivedRequestsProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private Security $security
    ) {
        $this->messageBus = $queryBus;
    }

    /** @return ReservationRequestResource[] */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        $views = $this->handle(new GetReceivedRequestsQuery($user->getId()));

        return array_map(function (ReservationRequestView $view) {
            $resource = new ReservationRequestResource();
            $resource->id = $view->id;
            $resource->scooterId = $view->scooterId;
            $resource->startAt = $view->startAt;
            $resource->endAt = $view->endAt;
            $resource->plate = $view->plate;
            $resource->requesterName = $view->requesterName;
            $resource->requesterEmail = $view->requesterEmail;
            $resource->status = $view->status;

            return $resource;
        }, $views);
    }
}