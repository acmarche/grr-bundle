<?php
/**
 * This file is part of grr5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 27/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Templating\Helper;

use Carbon\CarbonInterface;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Setting\SettingConstants;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Setting\Repository\SettingRepository;
use Grr\GrrBundle\TypeEntry\Repository\TypeEntryRepository;
use Twig\Environment;

class FrontHelper
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var TypeEntryRepository
     */
    private $typeEntryRepository;
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    public function __construct(
        Environment $twigEnvironment,
        TypeEntryRepositoryInterface $typeEntryRepository,
        SettingRepositoryInterface $settingRepository
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->typeEntryRepository = $typeEntryRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderCellDataDay(\DateTime $dateSelected, TimeSlot $timeSlot, RoomModel $roomModel): string
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
                        return $this->twigEnvironment->render(
                            '@grr_front/daily/_cell_day_data.html.twig',
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

        return $this->twigEnvironment->render(
            '@grr_front/daily/_cell_day_empty.html.twig',
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

    /**
     * @return int|string
     */
    public function periodicityTypeName(int $type)
    {
        return PeriodicityConstant::getTypePeriodicite($type);
    }

    public function weekNiceName(CarbonInterface $date): string
    {
        return $this->twigEnvironment->render(
            '@grr_front/weekly/_nice_name.html.twig',
            [
                'firstDay' => $firstDayWeek = $date->copy()->startOfWeek()->toMutable(),
                'lastDay' => $firstDayWeek = $date->copy()->endOfWeek()->toMutable(),
            ]
        );
    }

    public function legendTypeEntry(): string
    {
        $types = $this->typeEntryRepository->findAll();

        return $this->twigEnvironment->render(
            '@grr_front/_legend_entry_type.html.twig',
            ['types' => $types]
        );
    }

    public function companyName(): string
    {
        $company = $this->settingRepository->getValueByName(SettingConstants::COMPANY);

        return $company ?? 'Grr';
    }
}
