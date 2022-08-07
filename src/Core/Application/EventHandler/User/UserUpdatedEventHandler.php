<?php

declare(strict_types=1);

namespace App\Core\Application\EventHandler\User;

use App\Core\Domain\UserLog\UserLogRepositoryInterface;
use App\Core\Domain\User\UserUpdatedEvent;
use Psr\Log\LoggerInterface;

final class UserUpdatedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserLogRepositoryInterface $userLogRepository,
    ) {}

    public function __invoke(UserUpdatedEvent $event): void
    {
        $entity = $event->getEntity();
        $this->logger->info(sprintf('User %s was updated', $entity->getId()), ['changes' => $event->getChanges()]);
        $this->userLogRepository->store($entity->getId(), UserUpdatedEvent::class, $event->getChanges());
    }
}
