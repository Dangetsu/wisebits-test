<?php

declare(strict_types=1);

namespace App\Core\Application\Command\User;

final class DeleteUserCommand
{

    public function __construct(
        public readonly int $id,
    ) {}
}
