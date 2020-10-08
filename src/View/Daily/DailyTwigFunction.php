<?php

namespace Grr\GrrBundle\View\Daily;

use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\GrrBundle\Entity\Entry;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DailyTwigFunction extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateCellDataDay',
                function (\DateTime $dateSelected, TimeSlot $hour, RoomModel $roomModel): string {
                    return $this->renderCellDataDay($dateSelected, $hour, $roomModel);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function renderCellDataDay(\DateTime $dateSelected, TimeSlot $timeSlot, RoomModel $roomModel): string
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
