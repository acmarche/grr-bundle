<?php

namespace Grr\GrrBundle\Controller;

use Carbon\Carbon;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Binder\BindDataManager;
use Grr\GrrBundle\Navigation\Navigation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VueController extends AbstractController
{
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;
    /**
     * @var DateProvider
     */
    private $dateProvider;
    /**
     * @var BindDataManager
     */
    private $bindDataManager;

    public function __construct(
        EntryRepositoryInterface $entryRepository,
        AreaRepositoryInterface $areaRepository,
        DateProvider $dateProvider,
        BindDataManager $bindDataManager
    ) {
        $this->entryRepository = $entryRepository;
        $this->areaRepository = $areaRepository;
        $this->dateProvider = $dateProvider;
        $this->bindDataManager = $bindDataManager;
    }

    /**
     * @Route("/view2/area/{area}/date/{date}/view/{view}/room/{room}", name="grr_front_view", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     *
     * @param Area|null $area
     *
     * @throws \Exception
     */
    public function view(Area $area, \DateTime $date, string $view, ?Room $room = null): Response
    {
        if (!$date) {
            $date = Carbon::today(); //todo carbonfactory
        }

        $dateSelected = Carbon::createFromFormat('Y-m-d', $date->format('Y-m-d'));
        $dateSelected->locale('fr'); //todo carbonfactory

        if (Navigation::VIEW_MONTHLY == $view) {
            return $this->render(
                '@grr_front/monthly/month.html.twig',
                [
                    'area' => $area,
                    'room' => $room,
                    'dateSelected' => $dateSelected,
                    'monthData' => '',
                    'view' => $view,
                ]
            );
        }

        if (Navigation::VIEW_WEEKLY == $view) {

            $days = DateProvider::daysOfWeek($dateSelected);
            $roomModels = $this->bindDataManager->bindWeek($dateSelected, $area, $room);

            return $this->render(
                '@grr_front/weekly/week.html.twig',
                [
                    'days' => $days,
                    'area' => $area, //pour lien add entry
                    'roomModels' => $roomModels,
                    'room' => $room,
                    'dateSelected' => $dateSelected,
                    'week' => $dateSelected->week(),
                    'view' => $view,
                ]
            );
        }

        if (Navigation::VIEW_DAILY == $view) {
            return $this->render(
                '@grr_front/daily/day.html.twig',
                [
                    'area' => $area,
                    'room' => $room,
                    'dateSelected' => $dateSelected,
                    'view' => $view,
                ]
            );
        }

        return $this->render(
            '@grr_front/monthly/month.html.twig',
            [
                'area' => $area,
                'room' => $room,
                'date' => $date,
                'carbon' => $dateSelected,
            ]
        );
    }
}
