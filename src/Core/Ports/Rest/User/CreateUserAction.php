<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Core\Application\Command\User\CreateUserCommand;
use App\Core\Application\Query\User\UserDTO;
use Nelmio\ApiDocBundle\Annotation\Model as NelmioModel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CreateUserAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route("/api/users", methods:["POST"])]
    #[OA\Parameter(name: 'body', in: 'query', required: true, schema: new OA\Schema(ref: new NelmioModel(type:UserDTO::class, groups: ['user_create'])))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: 'Create user',
        content: new OA\JsonContent(properties: [
            new OA\Property('id', type: 'integer'),
        ], type: 'object')
    )]
    #[OA\Tag(name: 'User')]
    public function __invoke(CreateUserRequest $request): Response
    {
        $command = new CreateUserCommand(
            $request->getName(),
            $request->getEmail(),
            $request->getNotes(),
        );
        /** @var int $id */
        $id = $this->handle($command);
        return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
    }
}
