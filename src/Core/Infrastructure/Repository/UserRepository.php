<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\User\User;
use App\Core\Domain\User\UserInterface;
use App\Core\Domain\User\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @psalm-method list<User> findAll()
 * @psalm-method list<User> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function allCount(): int
    {
        return $this->count([]);
    }

    public function findById(mixed $id, bool $forUpdate = false): ?UserInterface
    {
        return $this->find($id, $forUpdate ? LockMode::PESSIMISTIC_WRITE : null);
    }

    /**
     * @param int $limit
     * @param int|null $offset
     * @return UserInterface[]
     */
    public function findByPagination(int $limit, int $offset = null): array
    {
        return $this->findBy([], null, $limit, $offset);
    }

    public function create(string $name, string $email, ?string $notes = null): UserInterface
    {
        $user = new User($name, $email, $notes);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }

    public function update(UserInterface $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    public function remove(UserInterface $entity): void
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
