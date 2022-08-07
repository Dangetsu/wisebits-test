<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class AbstractEntityEvent
{

    /**
     * @param EntityInterface $entity
     * @param array<string, array> $changes
     */
    public function __construct(
        private readonly EntityInterface $entity,
        private readonly array $changes,
    ) {}

    public function getEntity(): EntityInterface
    {
        return $this->entity;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function hasChanges(string $field): bool
    {
        $value = $this->changes[$field] ?? null;
        return $value !== null;
    }
}