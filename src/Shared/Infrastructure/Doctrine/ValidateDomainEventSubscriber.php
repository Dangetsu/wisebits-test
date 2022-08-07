<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\AutomaticCheckAssertInterface;
use App\Shared\Domain\EntityInterface;
use App\Shared\Domain\Exception\ValidationFailedException;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidateDomainEventSubscriber implements EventSubscriber
{

    public function __construct(private readonly ValidatorInterface $validator) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->validateEntity($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->validateEntity($event);
    }

    private function validateEntity(LifecycleEventArgs $event): void
    {
        /** @var EntityInterface $entity */
        $entity = $event->getEntity();
        if ($entity instanceof AutomaticCheckAssertInterface) {
            $violationList = $this->validator->validate($entity);
            if ($violationList->count() > 0) {
                // todo:
                throw new ValidationFailedException('Validation error', $violationList);
            }
        }
    }
}