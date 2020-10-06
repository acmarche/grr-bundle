<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/03/19
 * Time: 11:35.
 */

namespace Grr\GrrBundle\Entry\Binder;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Entry\EntryLocationService;
use Grr\Core\Factory\DayFactory;
use Grr\Core\Model\Month;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Room\Repository\RoomRepository;

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
        EntryRepositoryInterface $entryRepository,
        RoomRepositoryInterface $roomRepository,
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
    public function bindMonth(Month $month, Area $area, Room $room = null): void
    {
        $entries = $this->entryRepository->findForMonth($month->firstOfMonth(), $area, $room);

        foreach ($month->getCalendarDays() as $date) {
            $day = $this->dayFactory->createFromCarbon($date);
            $events = $this->extractByDate($day, $entries);
            $day->addEntries($events);
            $month->addDataDay($day);
        }
    }

    /**
     * @param Room $room
     *
     * @return RoomModel[]
     *
     * @throws \Exception
     */
    public function bindWeek(DateTimeInterface $week, Area $area, Room $room = null): array
    {
        if (null !== $room) {
            $rooms = [$room];
        } else {
            $rooms = $this->roomRepository->findByArea($area); //not $area->getRooms() sqlite not work
        }

        $carbonPeriod = DateProvider::daysOfWeek($week);
        $data = [];

        foreach ($rooms as $room) {
            $roomModel = new RoomModel($room);
            foreach ($carbonPeriod as $dayCalendar) {
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
    public function bindDay(CarbonInterface $carbon, Area $area, array $timeSlots, Room $room = null): array
    {
        $roomsModel = [];

        if (null !== $room) {
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
