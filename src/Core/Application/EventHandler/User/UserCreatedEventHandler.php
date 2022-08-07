<?php

declare(strict_types=1);

namespace App\Core\Application\EventHandler\User;

use App\Core\Domain\User\UserCreatedEvent;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\UserLog\UserLogRepositoryInterface;
use Psr\Log\LoggerInterface;

final class UserCreatedEventHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserLogRepositoryInterface $userLogRepository,
    ) {}

    public function __invoke(UserCreatedEvent $event): void
    {
        $entity = $event->getEntity();
        $fields = $this->extractFieldsFromUser($entity);
        $this->logger->info(sprintf('User %s was created', $entity->getId()), $fields);
        $this->userLogRepository->store($entity->getId(), UserCreatedEvent::class, $fields);
    }

    /**
     * Извлекаем значения полей через рефлексию, т.к. нет гарантий, что сущность можно сериализовать в json
     */
    private function extractFieldsFromUser(UserInterface $user): array
    {
        $api = new \ReflectionClass($user);
        $fields = [];
        foreach ($api->getProperties() as $property)
        {
            $value = $property->getValue($user);
            if (is_scalar($value) || $value instanceof \DateTimeInterface) {
                $fields[$property->getName()] = $value;
            }
        }
        return $fields;
    }
}
