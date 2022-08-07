<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Exception\ValidationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequest
{
    protected Request $request;

    public function __construct(
        protected ValidatorInterface $validator,
        ?Request $request = null
    ) {
        $this->request = $request ?? Request::createFromGlobals();
        $this->populate();
        $this->validate();
    }

    private function validate(): void
    {
        $errors = $this->validator->validate($this);
        if ($errors->count() > 0) {
            throw new ValidationFailedException('Validation error', $errors);
        }
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    private function populate(): void
    {
        $data = $this->getRequest()->isMethod('POST') || $this->getRequest()->isMethod('PUT')
            ? $this->getRequest()->toArray()
            : $this->getRequest()->query->all();
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $trimmedValue = is_string($value) ? trim($value) : $value;
                $this->{$property} = $this->preparePropertyValue($property, $trimmedValue);
            }
        }
    }

    protected function preparePropertyValue(string $property, mixed $value): mixed
    {
        return $value;
    }
}