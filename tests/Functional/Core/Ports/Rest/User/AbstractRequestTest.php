<?php

namespace App\Tests\Functional\Core\Ports\Rest\User;

use App\Shared\Domain\Exception\ValidationFailedException;
use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequestTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @param array<string, string> $expectedViolationMessage
     * @param Request $request
     * @dataProvider requestProvider
     */
    public function testRequestsAsserts(array $expectedViolationMessage, Request $request): void
    {
        /** @var ValidatorInterface $validator */
        $validator = static::getContainer()->get(ValidatorInterface::class);
        try {
            $className = $this->requestClass();
            /** @var AbstractRequest $abstractRequest */
            $abstractRequest = new $className($validator, $request);
            $data = $request->isMethod('POST') || $request->isMethod('PUT')
                ? $request->toArray()
                : $request->query->all();
            $this->assertRequest($data, $abstractRequest);
        } catch (ValidationFailedException $exception) {
            $this->assertCount(count($expectedViolationMessage), $exception->getViolations(), 'Expected violations count doesn\'t match with actual.');
            /** @var ConstraintViolation $violation */
            foreach ($exception->getViolations() as $violation) {
                $this->assertEquals($expectedViolationMessage[$violation->getPropertyPath()], $violation->getMessage());
            }
        }
    }

    abstract protected function requestClass(): string;

    abstract protected function assertRequest(array $expectedData, AbstractRequest $request): void;

    abstract public function requestProvider(): array;
}