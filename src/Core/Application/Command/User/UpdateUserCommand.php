<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

final class UpdateUserCommand
{

    public function __construct(
        public readonly int $id,
        public readonly ?string $email,
        public readonly ?string $notes,
    ) {}
}
