<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest extends AbstractRequest
{
    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\Length(min: 8, max: 64)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9]+$/', message: 'This value is should contains only literals and numbers.')]
    #[Assert\NotBlank]
    protected $name;
    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Length(max: 256)]
    protected $email;

    /**
     * @var string|null
     */
    #[Assert\Type('string')]
    protected $notes;

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}