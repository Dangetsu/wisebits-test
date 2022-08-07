<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Core\Application\Command\User\DeleteUserCommand;
use App\Core\Application\Query\User\UserDTO;
use Nelmio\ApiDocBundle\Annotation\Model as NelmioModel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class DeleteUserAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $commandBus,
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route("/api/users/{id<\d+>}", methods:["DELETE"])]
    #[OA\Response(
        response: Response::HTTP_NO_CONTENT,
        description: 'Delete user',
        content: new OA\JsonContent(type: 'object')
    )]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'User is not found')]
    #[OA\Tag(name: 'User')]
    public function __invoke(int $id): Response
    {
        $this->handle(new DeleteUserCommand($id));
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
