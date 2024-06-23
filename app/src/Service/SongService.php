<?php
/**
 * Song service.
 */

namespace App\Service;

use App\Entity\Song;
use App\Repository\SongRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class SongService.
 */
class SongService implements SongServiceInterface
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
     * Constructor.
     *
     * @param SongRepository     $songRepository Song repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly SongRepository $songRepository, private readonly PaginatorInterface $paginator)
    {
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
            $this->songRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Song $song Song entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Song $song): void
    {
        $this->songRepository->save($song);
    }

    /**
     * Delete entity.
     *
     * @param Song $song Song entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Song $song): void
    {
        $this->songRepository->delete($song);
    }

}
