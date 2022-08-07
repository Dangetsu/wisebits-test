<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

interface HumanReadableInterface
{

    public function getSystemCode(): string;

    public function getReadableMessage(): string;

    public function getHttpCode(): int;
}