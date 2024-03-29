<?php

namespace Grr\GrrBundle\View\Weekly;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Model\DataDay;
use Grr\Core\Model\RoomModel;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewWeeklyRender implements ViewInterface
{
    public function __construct(
        private readonly Environment $environment,
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly CarbonFactory $carbonFactory,
        private readonly DateProvider $dateProvider
    ) {
    }

    public static function getDefaultIndexName(): string
    {
        return ViewInterface::VIEW_WEEKLY;
    }

    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $carbon = $this->carbonFactory->instance($dateSelected);

        $days = $this->dateProvider->daysOfWeek($carbon);
        $roomModels = $this->bindWeek($dateSelected, $area, $room);
        $weekNiceName = $this->weekNiceName($carbon);

        $content = $this->environment->render(
            '@grr_front/view/weekly/week.html.twig',
            [
                'days' => $days,
                'area' => $area,
                //pour lien add entry
                'roomModels' => $roomModels,
                'dateSelected' => $carbon,
                'weekNiceName' => $weekNiceName,
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
        if ($room instanceof RoomInterface) {
            $rooms = [$room];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        $carbonPeriod = $this->dateProvider->daysOfWeek(Carbon::instance($week));
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

    private function weekNiceName(CarbonInterface $date): string
    {
        return $this->environment->render(
            '@grr_front/view/weekly/_nice_name.html.twig',
            [
                'firstDay' => $firstDayWeek = $date->copy()->startOfWeek()->toMutable(),
                'lastDay' => $firstDayWeek = $date->copy()->endOfWeek()->toMutable(),
            ]
        );
    }
}
