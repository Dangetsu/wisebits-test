<?php

declare(strict_types=1);

namespace App\Shared\Domain;

final class Pagination
{
    public const DEFAULT_LIMIT = 50;
    public const DEFAULT_OFFSET = 0;

    public function __construct(
        public readonly int $limit = self::DEFAULT_LIMIT,
        public readonly int $offset = self::DEFAULT_OFFSET
    ) {}
}