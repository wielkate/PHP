<?php
/**
 * Song controller.
 */

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class SongController.
 */
#[Route('/song')]
class SongController extends AbstractController
{
    /**
     * Index action.
     *
     * @param SongRepository     $songRepository Song repository
     * @param PaginatorInterface $paginator      Paginator
     *
     * @return Response HTTP response
     */
    #[Route(name: 'song_index', methods: 'GET')]
    public function index(SongRepository $songRepository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $songRepository->queryAll(),
            $page,
            SongRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('song/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Song $song Song entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'song_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Song $song): Response
    {
        return $this->render(
            'song/show.html.twig',
            ['song' => $song]
        );
    }
}
