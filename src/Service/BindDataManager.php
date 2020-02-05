<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/03/19
 * Time: 11:35.
 */

namespace Grr\GrrBundle\Service;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Entry\EntryLocationService;
use Grr\Core\Factory\DayFactory;
use Grr\Core\Model\Month;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Model\Week;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Repository\EntryRepository;
use Grr\GrrBundle\Repository\RoomRepository;

class BindDataManager
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;
    /**
     * @var EntryLocationService
     */
    private $entryLocationService;
    /**
     * @var DayFactory
     */
    private $dayFactory;
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    public function __construct(
        EntryRepository $entryRepository,
        RoomRepository $roomRepository,
        EntryLocationService $entryLocationService,
        DayFactory $dayFactory
    ) {
        $this->entryRepository = $entryRepository;
        $this->entryLocationService = $entryLocationService;
        $this->dayFactory = $dayFactory;
        $this->roomRepository = $roomRepository;
    }

    /**
     * Va chercher toutes les entrées du mois avec les repetitions
     * Parcours tous les jours du mois
     * Crée une instance Day et set les entrées.
     * Ajouts des ces days au model Month.
     */
    public function bindMonth(Month $monthModel, Area $area, Room $room = null): void
    {
        $entries = $this->entryRepository->findForMonth($monthModel->firstOfMonth(), $area, $room);

        foreach ($monthModel->getCalendarDays() as $date) {
            $day = $this->dayFactory->createFromCarbon($date);
            $events = $this->extractByDate($day, $entries);
            $day->addEntries($events);
            $monthModel->addDataDay($day);
        }
    }

    /**
     * @param Room $roomSelected
     *
     * @return RoomModel[]
     *
     * @throws \Exception
     */
    public function bindWeek(Week $weekModel, Area $area, Room $roomSelected = null): array
    {
        if (null !== $roomSelected) {
            $rooms = [$roomSelected];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        $days = $weekModel->getCalendarDays();
        $data = [];

        foreach ($rooms as $room) {
            $roomModel = new RoomModel($room);
            foreach ($days as $dayCalendar) {
                $dataDay = $this->dayFactory->createFromCarbon($dayCalendar);
                $entries = $this->entryRepository->findForDay($dayCalendar, $room);
                $dataDay->addEntries($entries);
                $roomModel->addDataDay($dataDay);
            }
            $data[] = $roomModel;
        }

        return $data;
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
    public function bindDay(CarbonInterface $day, Area $area, array $timeSlots, Room $roomSelected = null): array
    {
        $roomsModel = [];

        if (null !== $roomSelected) {
            $rooms = [$roomSelected];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        foreach ($rooms as $room) {
            $roomModel = new RoomModel($room);
            $entries = $this->entryRepository->findForDay($day, $room);
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
                $count = count($entry->getLocations());
                $entry->setCellules($count);
            }
        }

        return $roomsModel;
    }

    /**
     * @return mixed[]
     */
    public function extractByDate(DateTimeInterface $dateTime, array $entries): array
    {
        $data = [];
        foreach ($entries as $entry) {
            if ($entry->getStartTime()->format('Y-m-d') === $dateTime->format('Y-m-d')) {
                $data[] = $entry;
            }
        }

        return $data;
    }
}
