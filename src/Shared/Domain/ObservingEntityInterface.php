<?php

declare(strict_types=1);

namespace App\Shared\Domain;

interface ObservingEntityInterface
{

    /**
     * @psalm-return class-string|null
     */
    public function getSubscribedEvent(EventEnum $eventEnum): ?string;
}