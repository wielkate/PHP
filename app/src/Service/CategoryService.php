<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\SongRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    /**
     * Song repository.
     */
    private readonly SongRepository $songRepository;

    /**
     * Constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param SongRepository     $_songRepository    Song Repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly SongRepository $_songRepository, private readonly PaginatorInterface $paginator)
    {
        $this->songRepository = $_songRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        if (is_null($category->getId())) {
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $category->setUpdatedAt(new \DateTimeImmutable());

        $this->categoryRepository->save($category);
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        $result = $this->songRepository->countByCategory($category);

        return $result <= 0;
    }

    /**
     * Find on by Id.
     *
     * @param int $id Id
     *
     * @return Category|null Category Object
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }
}
