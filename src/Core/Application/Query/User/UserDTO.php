<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User;

use App\Core\Domain\User\UserInterface;

final class UserDTO
{

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly \DateTimeImmutable $created,
        public readonly ?\DateTimeImmutable $deleted = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromEntity(UserInterface $user): self
    {
        return new self(
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getCreated(),
            $user->getDeleted(),
            $user->getNotes(),
        );
    }
}