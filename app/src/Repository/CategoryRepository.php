<?php
/**
 * Category repository.
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * CategoryRepository constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial category.{id, title, createdAt, updatedAt, slug}')
            ->orderBy('category.updatedAt', 'DESC');
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Category $category): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($category);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Category $category): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($category);
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
        return $queryBuilder ?? $this->createQueryBuilder('category');
    }
}
