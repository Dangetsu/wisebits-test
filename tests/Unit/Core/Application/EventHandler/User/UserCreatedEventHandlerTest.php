<?php

namespace App\Tests\Unit\Core\Application\EventHandler\User;

use App\Core\Application\EventHandler\User\UserCreatedEventHandler;
use App\Core\Domain\User\User;
use App\Core\Domain\User\UserCreatedEvent;
use App\Core\Domain\UserLog\UserLogRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class UserCreatedEventHandlerTest extends TestCase
{

    public function testCreateEvent(): void
    {
        $id = 6;
        $name = 'BillyBoy';
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";
        $created = new \DateTimeImmutable();

        $logger = $this->createMock(LoggerInterface::class);

        $userLogRepository = $this->createMock(UserLogRepositoryInterface::class);
        $userLogRepository->expects(self::once())->method('store')
            ->with(
                $id,
                type: UserCreatedEvent::class,
                fields: [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'notes' => $notes,
                    'created' => $created,
                ]
            );

        // Нет возможности указать свойства через мок
        $user = new User($name, $email, $notes);
        $api = new \ReflectionClass($user);
        $api->getProperty('id')->setValue($user, $id);
        $api->getProperty('created')->setValue($user, $created);

        $event = new UserCreatedEvent($user, []);
        $handler = new UserCreatedEventHandler($logger, $userLogRepository);
        $handler($event);
    }
}