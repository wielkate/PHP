<?php
/**
 * Song controller.
 */

namespace App\Controller;

use App\Entity\Song;
use App\Form\SongType;
use App\Service\SongServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SongController.
 */
#[Route('/song')]
class SongController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param SongServiceInterface $songService Song service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly SongServiceInterface $songService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'song_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->songService->getPaginatedList($page);

        return $this->render('song/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Song $song Song entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'song_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Song $song): Response
    {
        return $this->render('song/show.html.twig', ['song' => $song]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'song_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $song = new Song();
        $form = $this->createForm(
            SongType::class,
            $song,
            ['action' => $this->generateUrl('song_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->songService->save($song);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('song_index');
        }

        return $this->render('song/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Song    $song    Song entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'song_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    public function edit(Request $request, Song $song): Response
    {
        $form = $this->createForm(
            SongType::class,
            $song,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('song_edit', ['id' => $song->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->songService->save($song);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('song_index');
        }

        return $this->render(
            'song/edit.html.twig',
            [
                'form' => $form->createView(),
                'song' => $song,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Song    $song    Song
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'song_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Song $song): Response
    {
        $form = $this->createForm(
            FormType::class,
            $song,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('song_delete', ['id' => $song->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->songService->delete($song);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('song_index');
        }

        return $this->render(
            'song/delete.html.twig',
            [
                'form' => $form->createView(),
                'song' => $song,
            ]
        );
    }
}
