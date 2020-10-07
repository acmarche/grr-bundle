<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 21/03/19
 * Time: 11:35.
 */

namespace Grr\GrrBundle\Entry\Binder;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Entry\EntryLocationService;
use Grr\Core\Factory\DataDayFactory;
use Grr\Core\Model\DataDay;
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
     * @var DataDayFactory
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
        DataDayFactory $dayFactory
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
     *
     * @return DataDay[]
     */
    public function bindMonth(DateTimeInterface $dateSelected, Area $area, Room $room = null): array
    {
        $dateCarbon = Carbon::instance($dateSelected);
        $monthEntries = $this->entryRepository->findForMonth($dateCarbon->firstOfMonth(), $area, $room);
        $dataDays = [];

        foreach (DateProvider::daysOfMonth($dateSelected) as $date) {
            $dataDay = new DataDay($date);
            $entries = $this->extractByDate($date, $monthEntries);
            $dataDay->addEntries($entries);
            $dataDays[$date->toDateString()] = $dataDay;
        }

        return $dataDays;
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
     * @return EntryInterface[]
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
