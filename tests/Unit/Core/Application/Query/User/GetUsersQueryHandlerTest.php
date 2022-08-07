<?php

namespace App\Tests\Unit\Core\Application\Query\User;

use App\Core\Application\Query\User\GetUsersQuery;
use App\Core\Application\Query\User\GetUsersQueryHandler;
use App\Core\Application\Query\User\UserDTO;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Paginated;
use App\Shared\Domain\Pagination;
use PHPUnit\Framework\TestCase;

final class GetUsersQueryHandlerTest extends TestCase
{

    public function testGetUsers(): void
    {
        $pagination = new Pagination(limit: 50, offset: 5);
        $totalCount = 6;

        $id = 6;
        $name = 'BillyBoy';
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";
        $created = new \DateTimeImmutable();

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('getId')->willReturn($id);
        $user->expects(self::once())->method('getName')->willReturn($name);
        $user->expects(self::once())->method('getEmail')->willReturn($email);
        $user->expects(self::once())->method('getNotes')->willReturn($notes);
        $user->expects(self::once())->method('getCreated')->willReturn($created);

        $expectedResponse = new Paginated(
            [new UserDTO($id, $name, $email, $created, null, $notes)],
            $totalCount
        );

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findByPagination')->with($pagination->limit, $pagination->offset)
            ->willReturn([$user]);
        $userRepository->expects(self::once())->method('allCount')
            ->willReturn($totalCount);

        $query = new GetUsersQuery($pagination);
        $handler = new GetUsersQueryHandler($userRepository);
        $paginatedDTOs = $handler($query);
        $this->assertEquals($expectedResponse, $paginatedDTOs);
    }
}