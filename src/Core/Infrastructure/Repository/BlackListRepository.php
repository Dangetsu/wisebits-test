<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\BlackList\BlackList;
use App\Shared\Domain\DictionaryValueCheckerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlackList>
 *
 * @method BlackList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlackList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlackList[]    findAll()
 * @method BlackList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @psalm-method list<BlackList> findAll()
 * @psalm-method list<BlackList> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class BlackListRepository extends ServiceEntityRepository implements DictionaryValueCheckerInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlackList::class);
    }

    public function exists(string $type, string $value): bool
    {
        // Из коробки у доктрины нет ни INSTR ни LOCATE, чтобы не запариваться с их добавлением, просто делаем RAW запрос
        $sql = 'SELECT id FROM black_list WHERE type = :type AND INSTR(:value, value) > 0';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $result = $stmt->executeQuery(['type' => $type, 'value' => $value]);
        return $result->fetchOne() !== false;
    }
}
