<?php

namespace App\Tests\Unit\Core\Application\EventHandler\User;

use App\Core\Application\EventHandler\User\UserUpdatedEventHandler;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserUpdatedEvent;
use App\Core\Domain\UserLog\UserLogRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class UserUpdatedEventHandlerTest extends TestCase
{

    public function testUpdateEvent(): void
    {
        $id = 6;
        $changes = ['email' => ['vlad@some.com', 'myboy@harr.com'], 'notes' => [null, 'Hahaha']];

        $user = $this->createMock(UserInterface::class);
        $user->expects(self::atLeastOnce())->method('getId')
            ->willReturn($id);

        $logger = $this->createMock(LoggerInterface::class);

        $userLogRepository = $this->createMock(UserLogRepositoryInterface::class);
        $userLogRepository->expects(self::once())->method('store')
            ->with(
                $id,
                type: UserUpdatedEvent::class,
                fields: $changes
            );

        $event = new UserUpdatedEvent($user, $changes);
        $handler = new UserUpdatedEventHandler($logger, $userLogRepository);
        $handler($event);
    }
}