<?php

namespace Grr\GrrBundle\Controller;

use Carbon\Carbon;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
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

    public function __construct(
        EntryRepositoryInterface $entryRepository,
        AreaRepositoryInterface $areaRepository,
        DateProvider $dateProvider
    ) {
        $this->entryRepository = $entryRepository;
        $this->areaRepository = $areaRepository;
        $this->dateProvider = $dateProvider;
    }

    /**
     * @Route("/vue", name="vue")
     */
    public function index()
    {
        $carbon = Carbon::today();
        $weeks = $this->dateProvider->weeksOfMonth($carbon);

        return $this->render(
            'vue/index.html.twig',
            [
                'areas' => $this->areaRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/viewreidrect", name="grr_front_view_redirect")
     */
    public function viewRedirect()
    {
        $today = new \DateTime();

        return $this->redirectToRoute(
            'grr_front_view',
            [
                'area' => 4273,
                'date' => $today->format('Y-m-d'),
                'view' => Navigation::VIEW_MONTHLY,
            ]
        );
    }

    /**
     * @Route("/view2/area/{area}/date/{date}/view/{view}/room/{room}", name="grr_front_view", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     *
     * @param Area|null   $area
     * @param string|null $date
     */
    public function view(Area $area, \DateTime $date, string $view, ?Room $room = null): Response
    {
        if (!$date) {
            $date = Carbon::today();
        }

        $carbon = Carbon::instance($date);
        $carbon->locale('fr');

        if (Navigation::VIEW_MONTHLY == $view) {
            return $this->render(
                '@grr_front/monthly/month.html.twig',
                [
                    'area' => $area,
                    'room' => $room,
                    'dateSelected' => $carbon,
                    'monthData' => '',
                ]
            );
        }

        if (Navigation::VIEW_WEEKLY == $view) {
            return $this->render(
                '@grr_front/weekly/week.html.twig',
                [
                    'area' => $area,
                    'room' => $room,
                    'dateSelected' => $carbon,
                    'week' => $carbon->week(),
                ]
            );
        }

        if (Navigation::VIEW_DAILY == $view) {
            return $this->render(
                '@grr_front/daily/day.html.twig',
                [
                    'area' => $area,
                    'room' => $room,
                    'dateSelected' => $carbon,
                ]
            );
        }

        return $this->render(
            '@grr_front/monthly/month.html.twig',
            [
                'area' => $area,
                'room' => $room,
                'date' => $date,
                'carbon' => $carbon,
            ]
        );
    }
}
