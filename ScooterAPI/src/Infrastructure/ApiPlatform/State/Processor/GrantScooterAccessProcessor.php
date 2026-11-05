<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\Scooter\Command\GrantScooterAccess\GrantScooterAccessCommand;
use Src\Infrastructure\ApiPlatform\Resource\ScooterAccessResource;
use Src\Infrastructure\Security\SecurityUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<ScooterAccessResource, ScooterAccessResource>
 */
class GrantScooterAccessProcessor implements ProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private Security $security
    ) {}

    /**
     * @param ScooterAccessResource $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ScooterAccessResource
    {
        $user = $this->security->getUser();

        if (!$user instanceof SecurityUser) {
            throw new \LogicException('Utilisateur non authentifié.');
        }

        try {
            $this->commandBus->dispatch(new GrantScooterAccessCommand(
                requesterId: $user->getId(),
                scooterId: $data->scooterId,
                email: $data->email
            ));
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }

        return $data;
    }
}