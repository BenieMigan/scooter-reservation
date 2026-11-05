<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Scooter\Command\RegisterScooter\RegisterScooterCommand;
use Src\Infrastructure\ApiPlatform\Resource\ScooterResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ScooterResource, ScooterResource|null>
 */
class RegisterScooterProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ?ScooterResource {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(
                new RegisterScooterCommand(
                    ownerId: $user->getId(),
                    model: $data->model,
                    plate: $data->plate
                )
            );
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return null;
    }
}
