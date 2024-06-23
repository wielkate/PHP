<?php
/**
 * Song service interface.
 */

namespace App\Service;

use App\Entity\Song;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface SongServiceInterface.
 */
interface SongServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Song $song Song entity
     */
    public function save(Song $song): void;

    /**
     * Delete entity.
     *
     * @param Song $song Song entity
     */
    public function delete(Song $song): void;
}