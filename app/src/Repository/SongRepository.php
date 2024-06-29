<?php
/**
 * Song repository.
 */

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class SongRepository.
 *
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Song>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class SongRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in configuration files.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial song.{id, createdAt, updatedAt, title, duration, category}',
                'partial category.{id, title}',
            )
            ->leftJoin('song.category', 'category')
            ->orderBy('song.updatedAt', 'DESC');
    }

    /**
     * Query records by category.
     *
     * @param Category $category Category entity
     *
     * @return QueryBuilder Query builder
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('song.id'))
            ->where('song.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Song $song Song entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Song $song): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($song);
        $this->_em->flush();
    }

    /**
     * @param int $id param
     *
     * @return Song|null return
     */
    public function findOneById(int $id): ?Song
    {
        return $this->find($id);
    }

    /**
     * Delete entity.
     *
     * @param Song $song Song entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Song $song): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($song);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('song');
    }
}
