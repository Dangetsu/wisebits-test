<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface SafeDeleteEntityInterface
{

    public function getDeleted(): ?\DateTimeImmutable;

    public function markDeleted(): void;
}