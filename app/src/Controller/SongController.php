<?php
/**
 * Song controller.
 */

namespace App\Controller;

use App\Entity\Song;
use App\Repository\SongRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class SongController.
 */
#[Route('/song')]
class SongController extends AbstractController
{
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
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'song_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->paginator->paginate(
            $this->songRepository->queryAll(),
            $request->query->getInt('page', 1),
            SongRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('song/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Song $song Song
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'song_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', ['song' => $song]);
    }
}
