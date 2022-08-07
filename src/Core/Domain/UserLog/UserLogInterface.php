<?php

declare(strict_types=1);

namespace App\Core\Domain\UserLog;

use App\Shared\Domain\EntityInterface;

interface UserLogInterface extends EntityInterface
{

    public function getUserId(): int;

    public function getCreated(): \DateTimeImmutable;

    public function getType(): string;

    public function getLog(): array;
}