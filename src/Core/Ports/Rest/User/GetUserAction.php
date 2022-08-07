<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Core\Application\Query\User\GetUserQuery;
use App\Core\Application\Query\User\UserDTO;
use Nelmio\ApiDocBundle\Annotation\Model as NelmioModel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GetUserAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private readonly NormalizerInterface $normalizer
    ) {
        $this->messageBus = $queryBus;
    }

    #[Route("/api/users/{id<\d+>}", name: 'api_user_get', methods: ["GET"])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns user by id',
        content: new OA\JsonContent(properties: [
            new OA\Property('data', ref: new NelmioModel(type:UserDTO::class, groups: ['user_view'])),
        ], type: 'object')
    )]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'User is not found')]
    #[OA\Tag(name: 'User')]
    public function __invoke(int $id): Response
    {
        /** @var UserDTO $userDTO */
        $userDTO = $this->handle(new GetUserQuery($id));
        return new JsonResponse([
            'data' => $this->normalizer->normalize($userDTO, '', ['groups' => 'user_view']),
        ]);
    }
}
