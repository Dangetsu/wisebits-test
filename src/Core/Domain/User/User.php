<?php

declare(strict_types=1);

namespace App\Core\Domain\User;

use App\Core\Infrastructure\Repository\UserRepository;
use App\Shared\Domain\AutomaticCheckAssertInterface;
use App\Shared\Domain\DictionaryValueCheckerInterface;
use App\Shared\Domain\EventEnum;
use App\Shared\Domain\ObservingEntityInterface;
use App\Shared\Domain\SafeDeleteEntityInterface;
use App\Shared\Infrastructure\Constraint\NonInDictionary;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'users_email_uindex', columns: ['email'])]
#[ORM\UniqueConstraint(name: 'users_name_uindex', columns: ['name'])]
#[UniqueEntity('name')]
#[UniqueEntity('email')]
final class User implements
    UserInterface,
    SafeDeleteEntityInterface,
    ObservingEntityInterface,
    AutomaticCheckAssertInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'created', type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $created;

    #[ORM\Column(name: 'deleted', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deleted = null;

    public function __construct(
        #[ORM\Column(name: 'name', type: 'string', length: 64)]
        #[Assert\Length(min: 8, max: 64)]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9]+$/', message: 'This value is should contains only literals and numbers.')]
        #[Assert\NotBlank]
        #[NonInDictionary(DictionaryValueCheckerInterface::TYPE_NAME)]
        private string $name,

        #[ORM\Column(name: 'email', type: 'string', length: 256)]
        #[Assert\Email]
        #[Assert\Length(max: 256)]
        #[Assert\NotBlank]
        #[NonInDictionary(DictionaryValueCheckerInterface::TYPE_EMAIL, message: 'The email "{{ string }}" hosted on blocked domain.')]
        private string $email,

        #[ORM\Column(name: 'notes', type: 'text', length: 65535, nullable: true)]
        private ?string $notes = null,
    ) {
        // доктрина не задействует конструктор для получения данных из базы,
        // так что это отработает только при инициализации нового объекта руками
        $this->created = new \DateTimeImmutable();
    }

    /**
     * @psalm-return class-string|null
     */
    public function getSubscribedEvent(EventEnum $eventEnum): ?string
    {
        return match($eventEnum)
        {
            EventEnum::CREATE => UserCreatedEvent::class,
            EventEnum::UPDATE => UserUpdatedEvent::class,
            default => null,
        };
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreated(): \DateTimeImmutable
    {
        return $this->created;
    }

    public function getDeleted(): ?\DateTimeImmutable
    {
        return $this->deleted;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function markDeleted(): void
    {
        $this->deleted = new \DateTimeImmutable();
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}
