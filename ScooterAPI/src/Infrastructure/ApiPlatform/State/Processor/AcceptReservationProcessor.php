<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Reservation\Command\AcceptReservation\AcceptReservationCommand;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class AcceptReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(new AcceptReservationCommand(
                ownerId: $user->getId(),
                reservationId: (int) $uriVariables['id']   // l'id vient de l'URL
            ));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return null;
    }
}