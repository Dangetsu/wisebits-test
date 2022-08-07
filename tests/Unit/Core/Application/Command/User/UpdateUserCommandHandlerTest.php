<?php

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\UpdateUserCommand;
use App\Core\Application\Command\User\UpdateUserCommandHandler;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Shared\Domain\Exception\DomainResourceNotFoundException;
use App\Shared\Domain\Exception\UpdatingDeletedEntityException;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandHandlerTest extends TestCase
{

    public function testTryUpdateNotFoundUser(): void
    {
        $id = 6;
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects(self::once())
            ->method('findById')
            ->with(
                $id,
                for_update: true
            )
            ->willReturn(null);

        $this->expectException(DomainResourceNotFoundException::class);

        $command = new UpdateUserCommand($id, $email, $notes);
        $handler = new UpdateUserCommandHandler($userRepository);
        $handler($command);
    }

    public function testTryUpdateDeletedUser(): void
    {
        $id = 6;
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";
        $deleted = new \DateTimeImmutable();

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('getDeleted')
            ->willReturn($deleted);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects(self::once())
            ->method('findById')
            ->with(
                $id,
                for_update: true
            )
            ->willReturn($user);

        $this->expectException(UpdatingDeletedEntityException::class);

        $command = new UpdateUserCommand($id, $email, $notes);
        $handler = new UpdateUserCommandHandler($userRepository);
        $handler($command);
    }

    public function testSuccessFullyUpdateUser(): void
    {
        $id = 6;
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('setEmail')->willReturnSelf();
        $user->expects(self::once())->method('setNotes')->willReturnSelf();

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id, for_update: true)
            ->willReturn($user);
        $userRepository->expects(self::once())->method('update')
            ->with(self::callback(static fn (UserInterface $paramUser): bool =>
                $paramUser === $user
            ));

        $command = new UpdateUserCommand($id, $email, $notes);
        $handler = new UpdateUserCommandHandler($userRepository);
        $handler($command);
    }

    public function testSuccessUpdateEmailUser(): void
    {
        $id = 6;
        $email = 'billy@gmail.com';

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('setEmail')->willReturnSelf();
        $user->expects(self::never())->method('setNotes');

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id, for_update: true)
            ->willReturn($user);
        $userRepository->expects(self::once())->method('update')
            ->with(self::callback(static fn (UserInterface $paramUser): bool =>
                $paramUser === $user
            ));

        $command = new UpdateUserCommand($id, $email, null);
        $handler = new UpdateUserCommandHandler($userRepository);
        $handler($command);
    }

    public function testSuccessUpdateNotesUser(): void
    {
        $id = 6;
        $notes = "Some \n multiline text \n";

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::never())->method('setEmail');
        $user->expects(self::once())->method('setNotes')->willReturnSelf();

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects(self::once())->method('findById')->with($id, for_update: true)
            ->willReturn($user);
        $userRepository->expects(self::once())->method('update')
            ->with(self::callback(static fn (UserInterface $paramUser): bool =>
                $paramUser === $user
            ));

        $command = new UpdateUserCommand($id, null, $notes);
        $handler = new UpdateUserCommandHandler($userRepository);
        $handler($command);
    }
}