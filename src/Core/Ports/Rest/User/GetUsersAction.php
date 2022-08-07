<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Core\Application\Query\User\GetUsersQuery;
use App\Core\Application\Query\User\UserDTO;
use App\Shared\Domain\Paginated;
use App\Shared\Domain\Pagination;
use Nelmio\ApiDocBundle\Annotation\Model as NelmioModel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GetUsersAction
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $queryBus,
        private readonly NormalizerInterface $normalizer
    ) {
        $this->messageBus = $queryBus;
    }

    #[Route("/api/users", methods:["GET"])]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', maximum: 50, minimum: 1))]
    #[OA\Parameter(name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 0))]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns list of users',
        content: new OA\JsonContent(properties: [
            new OA\Property('data', type: 'array', items: new OA\Items(ref: new NelmioModel(type:UserDTO::class, groups: ['user_view']))),
            new OA\Property('total', type: 'integer'),
        ], type: 'object')
    )]
    #[OA\Tag(name: 'User')]
    public function __invoke(GetUsersRequest $request): Response
    {
        $pagination = new Pagination(
            $request->getLimit() ?? Pagination::DEFAULT_LIMIT,
            $request->getOffset() ?? Pagination::DEFAULT_OFFSET,
        );
        /** @var Paginated $paginated */
        $paginated = $this->handle(new GetUsersQuery($pagination));
        return new JsonResponse([
            'data' => $this->normalizer->normalize($paginated->data, '', ['groups' => 'user_view']),
            'total' => $paginated->total,
        ]);
    }
}
