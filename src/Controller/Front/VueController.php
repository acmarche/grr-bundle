<?php

namespace Grr\GrrBundle\Controller\Front;

use DateTimeImmutable;
use Exception;
use Grr\Core\View\ViewLocator;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/front')]
class VueController extends AbstractController implements FrontControllerInterface
{
    public function __construct(
        private ViewLocator $viewLocator
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/area/{area}/date/{date}/view/{view}/room/{room}', name: 'grr_front_view', methods: ['GET'])]
    #[Entity(data: 'area', expr: 'repository.find(area)')]
    #[ParamConverter(data: 'room', class: Room::class, isOptional: true, options: [
        'id' => 'room',
    ])]
    public function view(Area $area, \DateTime|DateTimeImmutable $date, string $view, ?Room $room = null): Response
    {
        $renderService = $this->viewLocator->findViewerByView($view);

        return $renderService->render($date, $area, $room);
    }
}
