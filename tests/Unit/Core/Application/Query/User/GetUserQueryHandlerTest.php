<?php

namespace App\Tests\Unit\Core\Application\Query\User;

use App\Core\Application\Query\User\GetUserQuery;
use App\Core\Application\Query\User\GetUserQueryHandler;
use App\Core\Application\Query\User\UserDTO;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;
use PHPUnit\Framework\TestCase;

final class GetUserQueryHandlerTest extends TestCase
{

    public function testTryGetNotFoundUser(): void
    {
        $id = 6;

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id)
            ->willReturn(null);

        $this->expectException(DomainResourceNotFoundException::class);

        $query = new GetUserQuery($id);
        $handler = new GetUserQueryHandler($userRepository);
        $handler($query);
    }

    public function testGetUserById(): void
    {
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

        $expectedUserDTO = new UserDTO($id, $name, $email, $created, null, $notes);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id)
            ->willReturn($user);

        $query = new GetUserQuery($id);
        $handler = new GetUserQueryHandler($userRepository);
        $actualUserDTO = $handler($query);
        $this->assertEquals($expectedUserDTO, $actualUserDTO);
    }

    public function testGetDeletedUserById(): void
    {
        $id = 6;
        $name = 'BillyBoy';
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";
        $created = new \DateTimeImmutable();
        $deleted = new \DateTimeImmutable();

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('getId')->willReturn($id);
        $user->expects(self::once())->method('getName')->willReturn($name);
        $user->expects(self::once())->method('getEmail')->willReturn($email);
        $user->expects(self::once())->method('getNotes')->willReturn($notes);
        $user->expects(self::once())->method('getCreated')->willReturn($created);
        $user->expects(self::once())->method('getDeleted')->willReturn($deleted);

        $expectedUserDTO = new UserDTO($id, $name, $email, $created, $deleted, $notes);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id)
            ->willReturn($user);

        $query = new GetUserQuery($id);
        $handler = new GetUserQueryHandler($userRepository);
        $actualUserDTO = $handler($query);
        $this->assertEquals($expectedUserDTO, $actualUserDTO);
    }
}