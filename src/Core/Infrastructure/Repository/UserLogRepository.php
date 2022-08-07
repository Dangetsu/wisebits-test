<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\UserLog\UserLog;
use App\Core\Domain\UserLog\UserLogRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserLog>
 *
 * @method UserLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLog[]    findAll()
 * @method UserLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @psalm-method list<UserLog> findAll()
 * @psalm-method list<UserLog> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserLogRepository extends ServiceEntityRepository implements UserLogRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLog::class);
    }

    public function store(int $userId, string $type, array $changes = []): void
    {
        $entity = new UserLog($userId, $type, $changes);
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
