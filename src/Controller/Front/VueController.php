<?php

namespace Grr\GrrBundle\Controller\Front;

use DateTime;
use Exception;
use Grr\Core\View\ViewLocator;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapDateTime;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/front')]
class VueController extends AbstractController implements FrontControllerInterface
{
    public function __construct(
        private readonly ViewLocator $viewLocator
    ) {
    }

    #[Route(path: '/area/{area}/date/{date}/view/{view}/room/{room<\d+>?1}', name: 'grr_front_view', methods: ['GET'])]
    public function view(
        #[MapEntity(expr: 'repository.find(area)')]
        Area $area,
        #[MapDateTime(format: 'Y-m-d')]
        string $date,
        string $view,
        #[MapEntity(expr: 'repository.find(room)')]
        ?Room $room = null
    ): Response {

        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        try {
            $renderService = $this->viewLocator->findViewerByView($view);
        } catch (Exception $e) {
            return new Response('Erreur de chargement '.$e->getMessage());
        }

        return $renderService->render($dateTime, $area, $room);
    }
}
