<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;
use App\Shared\Domain\Exception\UpdatingDeletedEntityException;

final class UpdateUserCommandHandler
{

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function __invoke(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->findById($command->id, forUpdate: true);
        if ($user === null) {
            throw new DomainResourceNotFoundException("User with id {$command->id} isn't found.");
        }
        if ($user->getDeleted() !== null) {
            throw new UpdatingDeletedEntityException((string)$user->getId());
        }
        if ($command->email !== null) {
            $user->setEmail($command->email);
        }
        if ($command->notes !== null) {
            $user->setNotes($command->notes);
        }
        $this->userRepository->update($user);
    }
}
