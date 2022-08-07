<?php

declare(strict_types=1);

namespace App\Core\Application\Query\User;

use App\Shared\Domain\Pagination;

final class GetUsersQuery
{

    public function __construct(public readonly Pagination $pagination = new Pagination()) {}
}