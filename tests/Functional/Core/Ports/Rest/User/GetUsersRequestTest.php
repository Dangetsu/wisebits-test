<?php

namespace App\Tests\Functional\Core\Ports\Rest\User;

use App\Core\Ports\Rest\User\GetUsersRequest;
use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;

final class GetUsersRequestTest extends AbstractRequestTest
{
    protected function requestClass(): string
    {
        return GetUsersRequest::class;
    }

    /**
     * @param array $expectedData
     * @param GetUsersRequest $request
     * @return void
     */
    protected function assertRequest(array $expectedData, AbstractRequest $request): void
    {
        $this->assertEquals($expectedData['limit'] ?? null, $request->getLimit());
        $this->assertEquals($expectedData['offset'] ?? null, $request->getOffset());
    }

    public function requestProvider(): array
    {
        return [
            'empty' => [
                'violations' => [],
                'request' => new Request(),
            ],

            'limit_zero' => [
                'violations' => [
                    'limit' => 'This value should be greater than 0.',
                ],
                'request' => new Request(query: ['limit' => 0]),
            ],
            'limit_less_zero' => [
                'violations' => [
                    'limit' => 'This value should be greater than 0.',
                ],
                'request' => new Request(query: ['limit' => -12]),
            ],
            'limit_great_max' => [
                'violations' => [
                    'limit' => 'This value should be less than or equal to 50.',
                ],
                'request' => new Request(query: ['limit' => 51]),
            ],
            'limit_exactly_max' => [
                'violations' => [],
                'request' => new Request(query: ['limit' => 50]),
            ],

            'offset_as_text' => [
                'violations' => [
                    'offset' => 'This value should be of type integer.',
                ],
                'request' => new Request(query: ['offset' => 'fdsf']),
            ],
            'offset_zero' => [
                'violations' => [],
                'request' => new Request(query: ['offset' => 0]),
            ],
            'offset_less_zero' => [
                'violations' => [
                    'offset' => 'This value should be greater than or equal to 0.',
                ],
                'request' => new Request(query: ['offset' => -12]),
            ],
            'offset_large_number' => [
                'violations' => [],
                'request' => new Request(query: ['offset' => 500000]),
            ],
        ];
    }
}