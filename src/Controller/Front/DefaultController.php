<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Helper\MonthHelperDataDisplay;
use Grr\Core\Provider\TimeSlotsProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Binder\BindDataManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FrontController.
 *
 * @Route("/front")
 */
class DefaultController extends AbstractController implements FrontControllerInterface
{
    /**
     * @var BindDataManager
     */
    private $bindDataManager;
    /**
     * @var MonthHelperDataDisplay
     */
    private $monthHelperDataDisplay;
    /**
     * @var TimeSlotsProvider
     */
    private $timeSlotsProvider;

    public function __construct(
        MonthHelperDataDisplay $monthHelperDataDisplay,
        BindDataManager $bindDataManager,
        TimeSlotsProvider $timeSlotsProvider
    ) {
        $this->bindDataManager = $bindDataManager;
        $this->monthHelperDataDisplay = $monthHelperDataDisplay;
        $this->timeSlotsProvider = $timeSlotsProvider;
    }

    public function monthly(Area $area, int $year, int $month, Room $room = null): Response
    {
        $monthModel = $this->monthFactory->create($year, $month);

        $this->bindDataManager->bindMonth($monthModel, $area, $room);

        $monthData = $this->monthHelperDataDisplay->generateHtmlMonth($monthModel, $area);

        return $this->render(
            '@grr_front/monthly/month.html.twig',
            [
                'firstDay' => $monthModel->firstOfMonth(),
                'area' => $area,
                'room' => $room,
                'monthData' => $monthData,
            ]
        );
    }
}
