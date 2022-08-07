<?php

namespace App\Tests\Unit\Core\Application\Command\User;

use App\Core\Application\Command\User\CreateUserCommand;
use App\Core\Application\Command\User\CreateUserCommandHandler;
use App\Core\Domain\User\User;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use Laminas\Code\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;

final class CreateUserCommandHandlerTest extends TestCase
{

    public function testSuccessCreateUser(): void
    {
        $id = 6;
        $name = 'BillyBoy';
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text \n";

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository
            ->expects(self::once())
            ->method('create')
            ->with(
                $name,
                $email,
                $notes,
            )
            ->willReturnCallback(function (string $name, string $email, ?string $notes = null) use ($id) {
                $user = new User($name, $email, $notes);
                $api = new ClassReflection($user);
                $api->getProperty('id')->setValue($user, $id);
                return $user;
            });

        $command = new CreateUserCommand($name, $email, $notes);
        $handler = new CreateUserCommandHandler($userRepository);
        $result = $handler($command);
        $this->assertSame($id, $result);
    }
}