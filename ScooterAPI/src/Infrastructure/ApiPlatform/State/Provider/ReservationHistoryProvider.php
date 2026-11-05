<?php

namespace Src\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Src\Application\Reservation\Query\GetReceivedRequests\ReservationRequestView;
use Src\Application\Reservation\Query\GetReservationHistory\GetReservationHistoryQuery;
use Src\Infrastructure\ApiPlatform\Resource\ReservationHistoryResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProviderInterface<ReservationHistoryResource>
 */
class ReservationHistoryProvider implements ProviderInterface
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private Security $security
    ) {
        $this->messageBus = $queryBus;
    }

    /** @return ReservationHistoryResource[] */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        $views = $this->handle(new GetReservationHistoryQuery($user->getId()));

        return array_map(function (ReservationRequestView $view) {
            $resource = new ReservationHistoryResource();
            $resource->id = $view->id;
            $resource->scooterId = $view->scooterId;
            $resource->plate = $view->plate;
            $resource->requesterName = $view->requesterName;
            $resource->requesterEmail = $view->requesterEmail;
            $resource->startAt = $view->startAt;
            $resource->endAt = $view->endAt;
            $resource->status = $view->status;
            $resource->refusalReason = $view->refusalReason;

            return $resource;
        }, $views);
    }
}