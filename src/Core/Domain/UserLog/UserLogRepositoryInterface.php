<?php

declare(strict_types=1);

namespace App\Core\Domain\UserLog;

interface UserLogRepositoryInterface
{

    public function store(int $userId, string $type, array $changes = []): void;
}