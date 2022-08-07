<?php

declare(strict_types=1);

namespace App\Core\Domain\User;

interface UserRepositoryInterface
{

    public function findById(mixed $id, bool $forUpdate = false): ?UserInterface;

    /**
     * @return UserInterface[]
     */
    public function findByPagination(int $limit, int $offset = null): array;

    public function allCount(): int;

    public function create(string $name, string $email, ?string $notes = null): UserInterface;

    public function update(UserInterface $entity): void;

    public function remove(UserInterface $entity): void;
}