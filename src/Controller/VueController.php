<?php

namespace Grr\GrrBundle\Controller;

use DateTime;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Templating\Helper\RenderViewLocator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VueController.
 *
 * @Route("/front")
 */
class VueController extends AbstractController
{
    /**
     * @var RenderViewLocator
     */
    private $renderViewLocator;

    public function __construct(RenderViewLocator $renderViewLocator)
    {
        $this->renderViewLocator = $renderViewLocator;
    }

    /**
     * @Route("/area/{area}/date/{date}/view/{view}/room/{room}", name="grr_front_view", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     *
     * @param Area|null $area
     *
     * @throws \Exception
     */
    public function view(Area $area, DateTime $date, string $view, ?Room $room = null): Response
    {
        $renderService = $this->renderViewLocator->loadInterfaceByKey($view);

        return $renderService->render($date, $area, $room);
    }
}
