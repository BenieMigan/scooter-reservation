<?php
namespace Src\Application\User\Command\RegisterUser;

use Src\Domain\User\Entity\User;
use Src\Domain\User\Repository\UserRepositoryInterface;
use Src\Application\User\Contract\PasswordHasherInterface;
use Src\Domain\User\Exception\EmailAlreadyUsedException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    
    public function __invoke(RegisterUserCommand $command): void
    {
        if ($this->userRepository->existsByEmail($command->email)) {
            throw new EmailAlreadyUsedException();
        }

        $user = new User(
            name: $command->name,
            email: $command->email,
            password: $this->passwordHasher->hash($command->password)
        );

        $this->userRepository->save($user);
    }
}