<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class Paginated
{

    public function __construct(
        public readonly array $data,
        public readonly int $total,
    ) {}
}