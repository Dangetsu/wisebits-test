<?php

namespace App\Tests\Functional\Core\Ports\Rest\User;

use App\Core\Application\Command\User\CreateUserCommand;
use App\Core\Application\Command\User\CreateUserCommandHandler;
use App\Core\Domain\User\User;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use App\Core\Ports\Rest\User\CreateUserAction;
use App\Core\Ports\Rest\User\CreateUserRequest;
use Laminas\Code\Reflection\ClassReflection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateUserActionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testCreateUser(): void
    {
        $id = 6;
        $name = 'BillyBoy';
        $email = 'billy@gmail.com';
        $notes = "Some \n multiline text";

        $command = new CreateUserCommand($name, $email, $notes);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects(self::once())->method('dispatch')
            ->with(self::callback(static fn (CreateUserCommand $sendingCommand): bool =>
                $sendingCommand->name === $command->name &&
                $sendingCommand->email === $command->email &&
                $sendingCommand->notes === $command->notes
            ))
            ->willReturnCallback(function () use ($command, $id) {
                $userRepository = $this->createMock(UserRepositoryInterface::class);
                $userRepository
                    ->expects(self::once())
                    ->method('create')
                    ->willReturnCallback(function (string $name, string $email, ?string $notes = null) use ($id) {
                        $user = new User($name, $email, $notes);
                        $api = new ClassReflection($user);
                        $api->getProperty('id')->setValue($user, $id);
                        return $user;
                    });

                $handler = new CreateUserCommandHandler($userRepository);
                $resultId = $handler($command);
                return new Envelope(new \stdClass(), [new HandledStamp($resultId, 'test')]);
            });

        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $createUserRequest = new CreateUserRequest(
            $validator,
            new Request(
                server: ['REQUEST_METHOD' => 'POST'],
                content: json_encode(['name' => $name, 'email' => $email, 'notes' => $notes], JSON_THROW_ON_ERROR)
            )
        );

        $action = new CreateUserAction($messageBus);
        $response = $action($createUserRequest);
        $this->assertEquals(new JsonResponse(['id' => $id], Response::HTTP_CREATED), $response);
    }
}