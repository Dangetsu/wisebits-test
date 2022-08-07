<?php

namespace App\Tests\Functional\Core\Ports\Rest\User;

use App\Core\Ports\Rest\User\UpdateUserRequest;
use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;

final class UpdateUserRequestTest extends AbstractRequestTest
{

    protected function requestClass(): string
    {
        return UpdateUserRequest::class;
    }

    /**
     * @param array $expectedData
     * @param UpdateUserRequest $request
     * @return void
     */
    protected function assertRequest(array $expectedData, AbstractRequest $request): void
    {
        $this->assertEquals($expectedData['email'] ?? null, $request->getEmail());
        $this->assertEquals($expectedData['notes'] ?? null, $request->getNotes());
    }

    public function requestProvider(): array
    {
        return [
            'empty' => [
                'violations' => [
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(),
            ],

            'invalid_email' => [
                'violations' => [
                    'email' => 'This value is not a valid email address.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"email": "vladl.ru"}'),
            ],
            'email_great_max' => [
                'violations' => [
                    'email' => 'This value is too long. It should have 256 characters or less.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"email": "vlaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@adl.ru"}'),
            ],

            'notes_as_number' => [
                'violations' => [
                    'notes' => 'This value should be of type string.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"notes":2145364}'),
            ],

            'min_valid_email' => [
                'violations' => [],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"email":"gb@j.com"}'),
            ],
            'min_valid_notes' => [
                'violations' => [],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"notes":"Тобою выбран неправильный тест-кейс!"}'),
            ],
            'max_valid' => [
                'violations' => [],
                'request' => new Request(server: ['REQUEST_METHOD' => 'PUT'], content: '{"email":"gb@j.com", "notes":"Тобою выбран неправильный тест-кейс!"}'),
            ],
        ];
    }
}