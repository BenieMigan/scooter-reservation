<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Reservation\Command\RequestReservation\RequestReservationCommand;
use Src\Infrastructure\ApiPlatform\Resource\ReservationRequestResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ReservationRequestResource, ReservationRequestResource>
 */
class RequestReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    /**
     * @param ReservationRequestResource $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReservationRequestResource
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(new RequestReservationCommand(
                requesterId: $user->getId(),
                scooterId: $data->scooterId,
                startAt: $data->startAt,
                endAt: $data->endAt
            ));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return $data;
    }
}