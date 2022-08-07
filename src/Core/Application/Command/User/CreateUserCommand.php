<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

final class CreateUserCommand
{

    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $notes,
    ) {}
}
