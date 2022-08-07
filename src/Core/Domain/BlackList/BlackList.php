<?php

declare(strict_types=1);

namespace App\Core\Domain\BlackList;

use App\Core\Infrastructure\Repository\BlackListRepository;
use App\Shared\Domain\AutomaticCheckAssertInterface;
use App\Shared\Domain\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlackListRepository::class)]
#[ORM\Table(name: 'black_list')]
#[ORM\UniqueConstraint(name: 'black_list_type_value_uindex', columns: ['type', 'value'])]
#[UniqueEntity(['type', 'value'])]
final class BlackList implements EntityInterface, AutomaticCheckAssertInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    public function __construct(
        #[ORM\Column(name: 'type', type: 'string', length: 10)]
        #[Assert\NotBlank]
        private string $type,
        #[ORM\Column(name: 'value', type: 'string', length: 255)]
        #[Assert\NotBlank]
        private string $value
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
