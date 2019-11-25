<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Factory\DayFactory;
use Grr\Core\Factory\MonthFactory;
use Grr\Core\Factory\WeekFactory;
use Grr\Core\Helper\MonthHelperDataDisplay;
use Grr\Core\Provider\TimeSlotsProvider;
use Grr\GrrBundle\Setting\SettingsProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Service\BindDataManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @var SettingsProvider
     */
    private $settingsProvider;
    /**
     * @var MonthHelperDataDisplay
     */
    private $monthHelperDataDisplay;
    /**
     * @var TimeSlotsProvider
     */
    private $timeSlotsProvider;
    /**
     * @var DayFactory
     */
    private $dayFactory;
    /**
     * @var MonthFactory
     */
    private $monthFactory;
    /**
     * @var WeekFactory
     */
    private $weekFactory;

    public function __construct(
        SettingsProvider $settingservice,
        MonthHelperDataDisplay $monthHelperDataDisplay,
        BindDataManager $calendarDataManager,
        TimeSlotsProvider $timeSlotsProvider,
        DayFactory $dayFactory,
        MonthFactory $monthFactory,
        WeekFactory $weekFactory
    ) {
        $this->bindDataManager = $calendarDataManager;
        $this->settingsProvider = $settingservice;
        $this->monthHelperDataDisplay = $monthHelperDataDisplay;
        $this->timeSlotsProvider = $timeSlotsProvider;
        $this->dayFactory = $dayFactory;
        $this->monthFactory = $monthFactory;
        $this->weekFactory = $weekFactory;
    }

    /**
     * @Route("/monthview/area/{area}/year/{year}/month/{month}/room/{room}", name="grr_front_month", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     */
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

    /**
     * @Route("/weekview/area/{area}/year/{year}/month/{month}/week/{week}/room/{room}", name="grr_front_week", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     */
    public function weekly(Area $area, int $year, int $month, int $week, Room $room = null): Response
    {
        $weekModel = $this->weekFactory->create($year, $week);
        $roomModels = $this->bindDataManager->bindWeek($weekModel, $area, $room);

        return $this->render(
            '@grr_front/weekly/week.html.twig',
            [
                'week' => $weekModel,
                'area' => $area, //pour lien add entry
                'roomModels' => $roomModels,
            ]
        );
    }

    /**
     * @Route("/dayview/area/{area}/year/{year}/month/{month}/day/{day}/room/{room}", name="grr_front_day", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     */
    public function daily(Area $area, int $year, int $month, int $day, Room $room = null): Response
    {
        $dayModel = $this->dayFactory->createImmutable($year, $month, $day);

        $daySelected = $dayModel->toImmutable();

        $timeSlots = $this->timeSlotsProvider->getTimeSlotsModelByAreaAndDaySelected($area, $daySelected);
        $roomsModel = $this->bindDataManager->bindDay($daySelected, $area, $timeSlots, $room);

        return $this->render(
            '@grr_front/daily/day.html.twig',
            [
                'day' => $dayModel,
                'roomsModel' => $roomsModel,
                'area' => $area, //pour lien add entry
                'hoursModel' => $timeSlots,
            ]
        );
    }
}
