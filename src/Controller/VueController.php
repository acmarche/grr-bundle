<?php

namespace Grr\GrrBundle\Controller;

use Carbon\Carbon;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Room;
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
     * @Route("/view2/date/{date}/area/{area}/room/{room}", name="grr_front_view", methods={"GET"})
     * @Entity("area", expr="repository.find(area)")
     * @ParamConverter("room", options={"mapping": {"room": "id"}})
     */
    public function view(Area $area = null, \DateTime $date = null, Room $room = null): Response
    {
        if (!$date) {
            $carbon = Carbon::today();
        }

        $carbon = Carbon::instance($date);
        $this->getDays();

        $weeks = $this->dateProvider->weeksOfMonth($carbon);
        foreach ($weeks as $week) {
            foreach ($week as $day) {
                dump($day->day);
            }
        }

        return $this->render(
            '@grr_front/monthly/view.html.twig',
            [
                'date' => $date,
                'firstDay' => $carbon,
                'carbon' => $carbon,
            ]
        );
    }

    protected function getDays()
    {
        $carbon = Carbon::today();
        dump($carbon->day);
    }
}
