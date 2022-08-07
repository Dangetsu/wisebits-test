<?php

declare(strict_types=1);

namespace App\Core\Ports\Rest\User;

use App\Shared\Infrastructure\Http\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserRequest extends AbstractRequest
{

    /**
     * @var string
     */
    #[Assert\Type('string')]
    #[Assert\Email]
    #[Assert\Length(max: 256)]
    protected $email;

    /**
     * @var string|null
     */
    #[Assert\Type('string')]
    protected $notes;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}