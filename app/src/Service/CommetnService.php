<?php
/**
 * Comment service.
 */

namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\SongRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CommentService.
 */
class CommetnService implements CommentServiceInterface
{
    /**
     * Song repository.
     */
    private readonly SongRepository $songRepository;

    /**
     * Constructor.
     *
     * @param CommentRepository  $commentRepository Comment repository
     * @param SongRepository     $_songRepository   Song Repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(private readonly CommentRepository $commentRepository, private readonly SongRepository $_songRepository, private readonly PaginatorInterface $paginator)
    {
        $this->songRepository = $_songRepository;
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Comment entity
     */
    public function save(Comment $comment): void
    {
        if (is_null($comment->getId())) {
            $comment->setCreatedAt(new \DateTimeImmutable());
        }
        $comment->setUpdatedAt(new \DateTimeImmutable());

        $this->commentRepository->save($comment);
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment);
    }

    /**
     * Find on by Id.
     *
     * @param int $id Id
     *
     * @return Comment|null Comment Object
     */
    public function findOneById(int $id): ?Comment
    {
        return $this->commentRepository->find($id);
    }
}
