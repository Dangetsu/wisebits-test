<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User;

use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;

final class GetUserQueryHandler
{

    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function __invoke(GetUserQuery $query): UserDTO
    {
        $user = $this->userRepository->findById($query->id);
        if ($user === null) {
            throw new DomainResourceNotFoundException("User with id {$query->id} isn't found.");
        }
        return UserDTO::fromEntity($user);
    }
}