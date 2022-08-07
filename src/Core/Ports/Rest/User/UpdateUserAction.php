<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Core\Application\Command\User\UpdateUserCommand;
use App\Core\Application\Query\User\UserDTO;
use Nelmio\ApiDocBundle\Annotation\Model as NelmioModel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class UpdateUserAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route("/api/users/{id<\d+>}", methods:["PUT"])]
    #[OA\Parameter(name: 'body', in: 'query', required: true, schema: new OA\Schema(ref: new NelmioModel(type:UserDTO::class, groups: ['user_update'])))]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Update user',
        content: new OA\JsonContent(type: 'object')
    )]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'User is not found')]
    #[OA\Tag(name: 'User')]
    public function __invoke(int $id, UpdateUserRequest $request): Response
    {
        $command = new UpdateUserCommand(
            $id,
            $request->getEmail(),
            $request->getNotes(),
        );
        $this->handle($command);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
