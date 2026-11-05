<?php

namespace Src\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Src\Application\User\Command\RegisterUser\RegisterUserCommand;
use Src\Infrastructure\ApiPlatform\Resource\UserResource;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @implements ProcessorInterface<UserResource, UserResource>
 */
class RegisterUserProcessor implements ProcessorInterface 
{
    public function __construct(
        private MessageBusInterface $commandBus
    ) {}
    /**
     * @param UserResource $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        try {
            $this->commandBus->dispatch(new RegisterUserCommand(
                name: $data->name,
                email: $data->email,
                password: $data->password
            ));
        } catch (HandlerFailedException $e) {

            throw $e->getPrevious() ?? $e;
        }

        return $data;
    }
}