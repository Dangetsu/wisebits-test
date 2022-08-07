<?php

declare(strict_types=1);

namespace App\Core\Domain\UserLog;

use App\Core\Infrastructure\Repository\UserLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserLogRepository::class)]
#[ORM\Table(name: 'user_log')]
#[ORM\Index(columns: ['user_id'], name: 'user_log_user_id_index')]
final class UserLog implements UserLogInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    public function __construct(
        #[ORM\Column(name: 'user_id', type: 'integer')]
        private int $userId,
        #[ORM\Column(name: 'type', type: 'string', length: 255)]
        private string $type,
        #[ORM\Column(name: 'log', type: 'json')]
        private array $log
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLog(): array
    {
        return $this->log;
    }
}
