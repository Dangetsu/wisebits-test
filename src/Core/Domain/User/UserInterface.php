<?php

declare(strict_types=1);

namespace App\Core\Domain\User;

use App\Shared\Domain\EntityInterface;

interface UserInterface extends EntityInterface
{

    public function getName(): string;

    public function getEmail(): string;

    public function getCreated(): \DateTimeImmutable;

    public function getDeleted(): ?\DateTimeImmutable;

    public function getNotes(): ?string;

    public function setName(string $name): self;

    public function setEmail(string $email): self;

    public function setNotes(?string $notes): self;
}