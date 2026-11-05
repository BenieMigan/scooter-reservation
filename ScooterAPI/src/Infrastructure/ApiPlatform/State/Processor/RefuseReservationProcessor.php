<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Reservation\Command\RefuseReservation\RefuseReservationCommand;
use Src\Infrastructure\ApiPlatform\Resource\RefuseReservationInput;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class RefuseReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    /**
     * @param RefuseReservationInput $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(new RefuseReservationCommand(
                ownerId: $user->getId(),
                reservationId: (int) $uriVariables['id'],
                reason: $data->reason
            ));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return null;
    }
}