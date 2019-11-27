<?php
/**
 * This file is part of grr5 application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 27/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Grr\GrrBundle\Templating\Helper;


use Grr\Core\Model\Day;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Model\Week;
use Grr\Core\Setting\SettingConstants;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Repository\EntryTypeRepository;
use Grr\GrrBundle\Repository\SettingRepository;
use Twig\Environment;

class FrontHelper
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var EntryTypeRepository
     */
    private $entryTypeRepository;
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    public function __construct(
        Environment $twigEnvironment,
        EntryTypeRepository $entryTypeRepository,
        SettingRepository $settingRepository
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function grrGenerateCellDataDay(TimeSlot $hour, RoomModel $roomModel, Day $day): string
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
                if ($location === $hour) {
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
            ['position' => 999, 'area' => $area, 'room' => $room, 'day' => $day, 'hourModel' => $hour]
        );
    }


    /**
     * @return int|string
     */
    public function grrPeriodicityTypeName(int $type)
    {
        return PeriodicityConstant::getTypePeriodicite($type);
    }

    public function grrWeekNiceName(Week $week): string
    {
        return $this->twigEnvironment->render(
            '@grr_front/weekly/_nice_name.html.twig',
            ['week' => $week]
        );
    }

    public function grrLegendEntryType(Area $area): string
    {
        $types = $this->entryTypeRepository->findAll();

        return $this->twigEnvironment->render(
            '@grr_front/_legend_entry_type.html.twig',
            ['types' => $types]
        );
    }

    public function grrCompanyName(): string
    {
        $company = $this->settingRepository->getValueByName(SettingConstants::COMPANY);

        return $company ?? 'Grr';
    }
}