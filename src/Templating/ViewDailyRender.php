<?php


namespace Grr\GrrBundle\Templating;

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
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;
    /**
     * @var RoomRepositoryInterface
     */
    private $roomRepository;
    /**
     * @var EntryLocationService
     */
    private $entryLocationService;
    /**
     * @var TimeSlotsProvider
     */
    private $timeSlotsProvider;
    /**
     * @var CarbonFactory
     */
    private $carbonFactory;

    public function __construct(
        Environment $environment,
        EntryRepositoryInterface $entryRepository,
        AreaRepositoryInterface $areaRepository,
        RoomRepositoryInterface $roomRepository,
        EntryLocationService $entryLocationService,
        TimeSlotsProvider $timeSlotsProvider,
        CarbonFactory $carbonFactory
    ) {
        $this->environment = $environment;
        $this->entryRepository = $entryRepository;
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
        $this->entryLocationService = $entryLocationService;
        $this->timeSlotsProvider = $timeSlotsProvider;
        $this->carbonFactory = $carbonFactory;
    }

    public static function getDefaultIndexName(): string
    {
        return ViewInterface::VIEW_DAILY;
    }

    public function bindData(): void
    {
        // TODO: Implement bindData() method.
    }

    /**
     * @param DateTimeInterface $dateSelected
     * @param AreaInterface $area
     * @param RoomInterface|null $room
     * @return Response
     */
    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $carbon = $this->carbonFactory->instance($dateSelected);
        $timeSlots = $this->timeSlotsProvider->getTimeSlotsModelByAreaAndDaySelected($area, $carbon);
        $roomsModel = $this->bindDay($carbon, $area, $timeSlots, $room);

        $content = $this->environment->render(
            '@grr_front/view/daily/day.html.twig',
            [
                'area' => $area, //pour lien add entry
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
     * @param CarbonInterface $carbon
     * @param AreaInterface $area
     * @param TimeSlot[] $timeSlots
     *
     * @param RoomInterface|null $room
     * @return RoomModel[]
     */
    public function bindDay(
        CarbonInterface $carbon,
        AreaInterface $area,
        array $timeSlots,
        RoomInterface $room = null
    ): array {
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
}
