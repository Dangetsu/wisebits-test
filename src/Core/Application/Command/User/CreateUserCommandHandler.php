<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

use App\Core\Domain\User\UserRepositoryInterface;

final class CreateUserCommandHandler
{

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(CreateUserCommand $command): int
    {
        $user = $this->userRepository->create($command->name, $command->email, $command->notes);
        return $user->getId();
    }
}
