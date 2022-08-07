<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User;

final class GetUserQuery
{

    public function __construct(public readonly int $id) {}
}