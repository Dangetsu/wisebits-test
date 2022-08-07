<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\AbstractEntityEvent;
use App\Shared\Domain\EntityInterface;
use App\Shared\Domain\EventEnum;
use App\Shared\Domain\ObservingEntityInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

final class TriggerDomainEventSubscriber implements EventSubscriber
{
    /**
     * @var AbstractEntityEvent[]
     */
    private array $events = [];

    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus) {
        $this->eventBus = $eventBus;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->addEntityToEvents(EventEnum::CREATE, $event);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->addEntityToEvents(EventEnum::UPDATE, $event);
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $this->addEntityToEvents(EventEnum::DELETE, $event);
    }

    /**
     * Отправляем события в слушатели сразу по окончанию сохранения изменений сущностей.
     * Очищаем
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $events = $this->events;
        $this->events = [];
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    private function addEntityToEvents(EventEnum $eventType, LifecycleEventArgs $eventArgs): void
    {
        /** @var EntityInterface $entity */
        $entity = $eventArgs->getEntity();
        if (!$entity instanceof ObservingEntityInterface) {
            return;
        }

        $eventClass = $entity->getSubscribedEvent($eventType);
        if ($eventClass === null || !class_exists($eventClass)) {
            return;
        }
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();
        /** @var AbstractEntityEvent $event */
        $event = new $eventClass($entity, $uow->getEntityChangeSet($entity));
        $this->events[] = $event;
    }
}