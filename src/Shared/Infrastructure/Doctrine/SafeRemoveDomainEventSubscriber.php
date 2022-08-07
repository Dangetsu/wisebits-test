<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\EntityInterface;
use App\Shared\Domain\SafeDeleteEntityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

final class SafeRemoveDomainEventSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $event): void
    {
        /** @var EntityInterface $entity */
        $entity = $event->getEntity();
        if (!$entity instanceof SafeDeleteEntityInterface) {
            return;
        }
        if ($entity->getDeleted() === null) {
            // Устанавливаем маркер удаленности
            $entity->markDeleted();
            $event->getObjectManager()->flush();
        }
        // Отменяем удаление сущности
        $event->getObjectManager()->detach($entity);
    }
}