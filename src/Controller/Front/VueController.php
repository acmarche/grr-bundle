<?php

namespace Grr\GrrBundle\Controller\Front;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use DateTime;
use DateTimeImmutable;
use Exception;
use Grr\Core\View\ViewLocator;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(path: '/front')]
class VueController extends AbstractController implements FrontControllerInterface
{
    public function __construct(
        private readonly ViewLocator $viewLocator
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/area/{area}/date/{date}/view/{view}/room/{room}', name: 'grr_front_view', methods: ['GET'])]
    public function view(
        #[MapEntity(expr: 'repository.find(area)')]
        Area $area,
        DateTime|DateTimeImmutable $date,
        string $view,
        #[MapEntity(expr: 'repository.find(room)')]
        ?Room $room = null
    ): Response {
        $renderService = $this->viewLocator->findViewerByView($view);

        return $renderService->render($date, $area, $room);
    }
}
