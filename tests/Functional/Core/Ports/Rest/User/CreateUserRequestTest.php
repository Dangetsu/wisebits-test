<?php

namespace App\Tests\Functional\Core\Ports\Rest\User;

use App\Core\Ports\Rest\User\CreateUserRequest;
use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;

final class CreateUserRequestTest extends AbstractRequestTest
{

    protected function requestClass(): string
    {
        return CreateUserRequest::class;
    }

    /**
     * @param array $expectedData
     * @param CreateUserRequest $request
     * @return void
     */
    protected function assertRequest(array $expectedData, AbstractRequest $request): void
    {
        $this->assertEquals($expectedData['name'] ?? null, $request->getName());
        $this->assertEquals($expectedData['email'] ?? null, $request->getEmail());
        $this->assertEquals($expectedData['notes'] ?? null, $request->getNotes());
    }

    public function requestProvider(): array
    {
        return [
            'empty' => [
                'violations' => [
                    'name' => 'This value should not be blank.',
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(),
            ],
            'name_as_number' => [
                'violations' => [
                    'name' => 'This value should be of type string.',
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":10000000}'),
            ],
            'name_non_match_regex' => [
                'violations' => [
                    'name' => 'This value is should contains only literals and numbers.',
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"GHDNygi23$312"}'),
            ],
            'name_less_min' => [
                'violations' => [
                    'name' => 'This value is too short. It should have 8 characters or more.',
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"Test"}'),
            ],
            'name_exactly_min' => [
                'violations' => [
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"TestTest"}'),
            ],
            'name_great_max' => [
                'violations' => [
                    'name' => 'This value is too long. It should have 64 characters or less.',
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"Testoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooor"}'),
            ],
            'name_exactly_max' => [
                'violations' => [
                    'email' => 'This value should not be blank.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"Testoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo"}'),
            ],

            'invalid_email' => [
                'violations' => [
                    'name' => 'This value should not be blank.',
                    'email' => 'This value is not a valid email address.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"email": "vladl.ru"}'),
            ],
            'email_great_max' => [
                'violations' => [
                    'name' => 'This value should not be blank.',
                    'email' => 'This value is too long. It should have 256 characters or less.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"email": "vlaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa@adl.ru"}'),
            ],

            'notes_as_number' => [
                'violations' => [
                    'name' => 'This value should not be blank.',
                    'email' => 'This value should not be blank.',
                    'notes' => 'This value should be of type string.',
                ],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"notes":2145364}'),
            ],

            'min_valid' => [
                'violations' => [],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"GoodBoyJonny", "email":"gb@j.com"}'),
            ],
            'max_valid' => [
                'violations' => [],
                'request' => new Request(server: ['REQUEST_METHOD' => 'POST'], content: '{"name":"GoodBoyJonny", "email":"gb@j.com", "notes":"Тобой выбран неправильный тест-кейс!"}'),
            ],
        ];
    }
}