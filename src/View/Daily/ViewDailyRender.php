<?php

namespace Grr\GrrBundle\View\Daily;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Entry\EntryLocationService;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Provider\TimeSlotsProvider;
use Grr\GrrBundle\Entity\Entry;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewDailyRender implements ViewInterface
{
    public function __construct(
        private readonly Environment $environment,
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly EntryLocationService $entryLocationService,
        private readonly TimeSlotsProvider $timeSlotsProvider,
        private readonly CarbonFactory $carbonFactory
    ) {
    }

    public static function getDefaultIndexName(): string
    {
        return ViewInterface::VIEW_DAILY;
    }

    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $carbon = $this->carbonFactory->instance($dateSelected);
        $timeSlots = $this->timeSlotsProvider->getTimeSlotsModelByAreaAndDaySelected($area, $carbon);
        $roomsModel = $this->bindDay($carbon, $area, $timeSlots, $room);

        $content = $this->environment->render(
            '@grr_front/view/daily/day.html.twig',
            [
                'area' => $area,
                //pour lien add entry
                'room' => $room,
                'roomsModel' => $roomsModel,
                'dateSelected' => $carbon,
                'view' => self::getDefaultIndexName(),
                'timeSlots' => $timeSlots,
            ]
        );

        return new Response($content);
    }

    /**
     * Genere des RoomModel avec les entrées pour chaque Room
     * Puis pour chaque entrées en calcul le nbre de cellules qu'elle occupe
     * et sa localisation.
     *
     * @param TimeSlot[] $timeSlots
     *
     * @return RoomModel[]
     */
    public function bindDay(
        CarbonInterface $carbon,
        AreaInterface $area,
        array $timeSlots,
        RoomInterface $room = null
    ): array {
        $roomsModel = [];

        if ($room instanceof RoomInterface) {
            $rooms = [$room];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        foreach ($rooms as $room) {
            $roomModel = new RoomModel($room);
            $entries = $this->entryRepository->findForDay($carbon, $room);
            $roomModel->setEntries($entries);
            $roomsModel[] = $roomModel;
        }

        foreach ($roomsModel as $roomModel) {
            /**
             * @var Entry[]
             */
            $entries = $roomModel->getEntries();

            foreach ($entries as $entry) {
                $entry->setLocations($this->entryLocationService->getLocations($entry, $timeSlots));
                $count = \count($entry->getLocations());
                $entry->setCellules($count);
            }
        }

        return $roomsModel;
    }
}
