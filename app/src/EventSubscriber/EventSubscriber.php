<?php
/**
 * Event subscriber.
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Event subscriber.
 */
class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @param UrlGeneratorInterface $urlGenerator param
     * @param RequestStack          $requestStack param
     */
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly RequestStack $requestStack)
    {
    }

    /**
     * @param ExceptionEvent $event param
     *
     * @return void return
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            // Pobierz ścieżkę, która spowodowała wyjątek
            $request = $this->requestStack->getCurrentRequest();
            $path = $request->getPathInfo();
            $redirectRoute = $this->determineRedirectRoute($path);

            if (null !== $redirectRoute) {
                $url = $this->urlGenerator->generate($redirectRoute);
                $this->setFlashMessage('warning', 'Brak rekordu.');
                $response = new RedirectResponse($url);
            } else {
                $response = new Response('Page not found.', Response::HTTP_NOT_FOUND);
            }

            $event->setResponse($response);
        }
    }

    /**
     * @return string[] return
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param string $path param
     *
     * @return string|null return
     */
    private function determineRedirectRoute(string $path): ?string
    {
        if (preg_match('/\/category\/\d+/', $path)) {
            return 'category_index';
        }

        if (preg_match('/\/tag\/\d+/', $path)) {
            return 'app_tag_index';
        }

        if (preg_match('/\/song\/\d+/', $path)) {
            return 'song_index';
        }

        return null;
    }

    /**
     * @param string $type    param
     * @param string $message param
     *
     * @return void return
     */
    private function setFlashMessage(string $type, string $message)
    {
        $request = $this->requestStack->getCurrentRequest();
        $flashBag = $request->getSession()->getFlashBag();
        $flashBag->add($type, $message);
    }
}
