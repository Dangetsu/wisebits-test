<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;

final class DeleteUserCommandHandler
{

    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function __invoke(DeleteUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->id, forUpdate: true);
        if ($user === null) {
            throw new DomainResourceNotFoundException("User with id {$command->id} isn't found.");
        }
        $this->userRepository->remove($user);
    }
}
