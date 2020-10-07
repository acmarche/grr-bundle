<?php

namespace Grr\GrrBundle\Templating;

use Carbon\Carbon;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Front\ViewerInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Model\DataDay;
use Grr\Core\Model\RoomModel;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Navigation\Navigation;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewWeeklyRender implements ViewerInterface
{
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;
    /**
     * @var RoomRepositoryInterface
     */
    private $roomRepository;
    /**
     * @var CarbonFactory
     */
    private $carbonFactory;

    public function __construct(
        Environment $environment,
        EntryRepositoryInterface $entryRepository,
        RoomRepositoryInterface $roomRepository,
        CarbonFactory $carbonFactory
    ) {
        $this->environment = $environment;
        $this->entryRepository = $entryRepository;
        $this->roomRepository = $roomRepository;
        $this->carbonFactory = $carbonFactory;
    }

    public static function getDefaultIndexName(): string
    {
        return Navigation::VIEW_WEEKLY;
    }

    public function bindData(): void
    {
        // TODO: Implement bindData() method.
    }

    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $carbon = $this->carbonFactory->instance($dateSelected);

        $days = DateProvider::daysOfWeek($carbon);
        $roomModels = $this->bindWeek($dateSelected, $area, $room);

        $content = $this->environment->render(
            '@grr_front/weekly/week.html.twig',
            [
                'days' => $days,
                'area' => $area, //pour lien add entry
                'roomModels' => $roomModels,
                'dateSelected' => $carbon,
                'view' => self::getDefaultIndexName(),
            ]
        );

        return new Response($content);
    }

    /**
     * @return RoomModel[]
     */
    public function bindWeek(DateTimeInterface $week, AreaInterface $area, RoomInterface $room = null): array
    {
        if (null !== $room) {
            $rooms = [$room];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        $carbonPeriod = DateProvider::daysOfWeek(Carbon::instance($week));
        $data = [];

        foreach ($rooms as $room) {
            $roomModel = new RoomModel($room);
            foreach ($carbonPeriod as $dayCalendar) {
                $dataDay = new DataDay($dayCalendar);
                $entries = $this->entryRepository->findForDay($dayCalendar, $room);
                $dataDay->addEntries($entries);
                $roomModel->addDataDay($dataDay);
            }
            $data[] = $roomModel;
        }

        return $data;
    }
}
