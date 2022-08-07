<?php

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\DeleteUserCommand;
use App\Core\Application\Command\User\DeleteUserCommandHandler;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;
use PHPUnit\Framework\TestCase;

final class DeleteUserCommandHandlerTest extends TestCase
{

    public function testTryDeleteNotFoundUser(): void
    {
        $id = 6;

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id, for_update: true)
            ->willReturn(null);

        $this->expectException(DomainResourceNotFoundException::class);

        $command = new DeleteUserCommand($id);
        $handler = new DeleteUserCommandHandler($userRepository);
        $handler($command);
    }

    public function testSuccessDeleteUser(): void
    {
        $id = 6;

        $user = $this->createMock(UserInterface::class);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id, for_update: true)
            ->willReturn($user);
        $userRepository->expects(self::once())->method('remove')
            ->with(self::callback(static fn (UserInterface $paramUser): bool =>
                $paramUser === $user
            ));

        $command = new DeleteUserCommand($id);
        $handler = new DeleteUserCommandHandler($userRepository);
        $handler($command);
    }
}