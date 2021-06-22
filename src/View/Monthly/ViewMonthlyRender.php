<?php

namespace Grr\GrrBundle\View\Monthly;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Model\DataDay;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ViewMonthlyRender implements ViewInterface
{
    private Environment $environment;
    private EntryRepositoryInterface $entryRepository;
    private CarbonFactory $carbonFactory;
    private DateProvider $dateProvider;

    public function __construct(
        Environment $environment,
        EntryRepositoryInterface $entryRepository,
        CarbonFactory $carbonFactory,
        DateProvider $dateProvider
    ) {
        $this->environment = $environment;
        $this->entryRepository = $entryRepository;
        $this->carbonFactory = $carbonFactory;
        $this->dateProvider = $dateProvider;
    }

    public static function getDefaultIndexName(): string
    {
        return ViewInterface::VIEW_MONTHLY;
    }

    public function render(DateTimeInterface $dateSelected, AreaInterface $area, ?RoomInterface $room = null): Response
    {
        $dateCarbon = $this->carbonFactory->instance($dateSelected);
        $dataDays = $this->bindMonth($dateCarbon, $area, $room);
        $monthData = $this->generateHtmlMonth($dateCarbon, $dataDays);

        $string = $this->environment->render(
            '@grr_front/view/monthly/month.html.twig',
            [
                'area' => $area,
                'room' => $room,
                'dateSelected' => $dateCarbon,
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
    private function bindMonth(CarbonInterface $dateSelected, AreaInterface $area, RoomInterface $room = null): array
    {
        $monthEntries = $this->entryRepository->findForMonth($dateSelected->firstOfMonth(), $area, $room);
        $dataDays = [];

        foreach ($this->dateProvider->daysOfMonth($dateSelected) as $date) {
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

    /**
     * @param DataDay[] $dataDays
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateHtmlMonth(CarbonInterface $dateSelected, array $dataDays): string
    {
        $weeks = $this->dateProvider->weeksOfMonth($dateSelected);

        return $this->environment->render(
            '@grr_front/view/monthly/_calendar_data.html.twig',
            [
                'weekDaysName' => $this->dateProvider->weekDaysName(),
                'firstDay' => $dateSelected->copy()->firstOfMonth(),
                'dataDays' => $dataDays,
                'weeks' => $weeks,
            ]
        );
    }
}
