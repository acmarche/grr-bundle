<?php

namespace Grr\GrrBundle\View\Daily;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\GrrBundle\Entity\Entry;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DailyTwigFunction extends AbstractExtension
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateCellDataDay',
                fn (DateTime $dateSelected, TimeSlot $hour, RoomModel $roomModel): string => $this->renderCellDataDay($dateSelected, $hour, $roomModel),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @param DateTime|DateTimeImmutable $dateSelected
     */
    private function renderCellDataDay(DateTimeInterface $dateSelected, TimeSlot $timeSlot, RoomModel $roomModel): string
    {
        /**
         * @var Entry[]
         */
        $entries = $roomModel->getEntries();
        foreach ($entries as $entry) {
            /**
             * @var TimeSlot[]
             */
            $locations = $entry->getLocations();
            $position = 0;
            foreach ($locations as $location) {
                if ($location === $timeSlot) {
                    if (0 === $position) {
                        return $this->environment->render(
                            '@grr_front/view/daily/_cell_day_data.html.twig',
                            ['position' => $position, 'entry' => $entry]
                        );
                    }

                    return '';
                }
                ++$position;
            }
        }

        $room = $roomModel->getRoom();
        $area = $room->getArea();

        return $this->environment->render(
            '@grr_front/view/daily/_cell_day_empty.html.twig',
            [
                'position' => 999,
                'area' => $area,
                'room' => $room,
                'hourBegin' => $timeSlot->getBegin()->hour,
                'minuteBegin' => $timeSlot->getBegin()->minute,
                'day' => $dateSelected,
            ]
        );
    }
}
