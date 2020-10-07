<?php


namespace Grr\GrrBundle\Templating;

use Carbon\Carbon;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Front\ViewerInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Helper\MonthHelperDataDisplay;
use Grr\Core\Model\DataDay;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Navigation\Navigation;
use Grr\GrrBundle\Templating\Helper\RenderViewLocator;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ViewMonthlyRender implements ViewerInterface
{
    /**
     * @var MonthHelperDataDisplay
     */
    private $monthHelperDataDisplay;
    /**
     * @var RenderViewLocator
     */
    private $renderFront;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    public function __construct(
        Environment $environment,
        EntryRepositoryInterface $entryRepository,
        MonthHelperDataDisplay $monthHelperDataDisplay,
        RenderViewLocator $renderFront
    ) {
        $this->monthHelperDataDisplay = $monthHelperDataDisplay;
        $this->renderFront = $renderFront;
        $this->environment = $environment;
        $this->entryRepository = $entryRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return Navigation::VIEW_MONTHLY;
    }

    public function bindData(): void
    {
        // TODO: Implement bindData() method.
    }

    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $dataDays = $this->bindMonth($dateSelected, $area, $room);
        $monthData = $this->monthHelperDataDisplay->generateHtmlMonth($dateSelected, $dataDays);

        $string = $this->environment->render(
            '@grr_front/monthly/month.html.twig',
            [
                'area' => $area,
                'room' => $room,
                'dateSelected' => $dateSelected,
                'monthData' => $monthData,
                'view' => self::getDefaultIndexName(),
            ]
        );

        return new Response($string);
    }

    /**
     * Va chercher toutes les entrées du mois avec les repetitions
     * Parcours tous les jours du mois
     * Crée une instance Day et set les entrées.
     * Ajouts des ces days au model Month.
     *
     * @return DataDay[]
     */
    private function bindMonth(DateTimeInterface $dateSelected, AreaInterface $area, RoomInterface $room = null): array
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
     * @return EntryInterface[]
     */
    private function extractByDate(DateTimeInterface $dateTime, array $entries): array
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
