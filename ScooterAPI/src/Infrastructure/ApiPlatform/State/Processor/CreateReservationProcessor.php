<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Reservation\Command\CreateReservation\CreateReservationCommand;
use Src\Infrastructure\ApiPlatform\Resource\ReservationResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ReservationResource, ReservationResource>
 */
class CreateReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    /**
     * @param ReservationResource $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReservationResource
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(new CreateReservationCommand(
                requesterId: $user->getId(),
                scooterId: $data->scooterId,
                email: $data->email,
                startAt: $data->startAt,
                endAt: $data->endAt
            ));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return $data;
    }
}