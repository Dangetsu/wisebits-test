<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User;

use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Paginated;

final class GetUsersQueryHandler
{

    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function __invoke(GetUsersQuery $query): Paginated
    {
        $allUsersCount = $this->userRepository->allCount();
        $users = $this->userRepository->findByPagination($query->pagination->limit, $query->pagination->offset);
        $userDTOs = array_map(static fn (UserInterface $user) => UserDTO::fromEntity($user), $users);
        return new Paginated($userDTOs, $allUsersCount);
    }
}