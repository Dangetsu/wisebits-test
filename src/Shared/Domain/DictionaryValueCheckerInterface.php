<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface DictionaryValueCheckerInterface
{
    public const TYPE_NAME  = 'name';
    public const TYPE_EMAIL = 'email';

    public function exists(string $type, string $value): bool;
}