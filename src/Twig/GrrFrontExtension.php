<?php

namespace Grr\GrrBundle\Twig;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\Core\Factory\MonthFactory;
use Grr\Core\Model\Day;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Model\Week;
use Grr\GrrBundle\Navigation\MenuGenerator;
use Grr\GrrBundle\Navigation\NavigationManager;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Repository\EntryTypeRepository;
use Grr\GrrBundle\Repository\SettingRepository;
use Grr\Core\Setting\SettingConstants;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GrrFrontExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var NavigationManager
     */
    private $navigationManager;
    /**
     * @var MenuGenerator
     */
    private $menuGenerator;
    /**
     * @var EntryTypeRepository
     */
    private $entryTypeRepository;
    /**
     * @var SettingRepository
     */
    private $settingRepository;
    /**
     * @var MonthFactory
     */
    private $monthFactory;

    public function __construct(
        RequestStack $requestStack,
        MenuGenerator $menuGenerator,
        Environment $twigEnvironment,
        NavigationManager $navigationManager,
        EntryTypeRepository $entryTypeRepository,
        SettingRepository $settingRepository,
        MonthFactory $monthFactory
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->navigationManager = $navigationManager;
        $this->requestStack = $requestStack;
        $this->menuGenerator = $menuGenerator;
        $this->entryTypeRepository = $entryTypeRepository;
        $this->settingRepository = $settingRepository;
        $this->monthFactory = $monthFactory;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrPeriodicityTypeName', function (int $type) {
                    return $this->grrPeriodicityTypeName($type);
                }, ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'grrWeekNiceName', function (Week $week): string {
                    return $this->grrWeekNiceName($week);
                }, ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * todo navigation function to same package.
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrMonthNavigationRender', function (): string {
                    return $this->monthNavigationRender();
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'grrMenuNavigationRender', function (): string {
                    return $this->menuNavigationRender();
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'grrGenerateCellDataDay', function (TimeSlot $hour, RoomModel $roomModel, Day $day): string {
                    return $this->grrGenerateCellDataDay($hour, $roomModel, $day);
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'grrLegendEntryType', function (Area $area): string {
                    return $this->grrLegendEntryType($area);
                }, ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'grrCompanyName', function (): string {
                    return $this->grrCompanyName();
                }
            ),
        ];
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
     * @return \Symfony\Component\HttpFoundation\Response|string
     *
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function monthNavigationRender()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return new Response('');
        }

        $year = $request->get('year') ?? 0;
        $month = $request->get('month') ?? 0;

        $monthModel = $this->monthFactory->create($year, $month);

        $navigation = $this->navigationManager->createMonth($monthModel);

        return $this->twigEnvironment->render(
            '@grr_front/navigation/month/_calendar_navigation.html.twig',
            [
                'navigation' => $navigation,
                'monthModel' => $monthModel,
            ]
        );
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function menuNavigationRender(): string
    {
        $form = $this->menuGenerator->generateMenuSelect();

        return $this->twigEnvironment->render(
            '@grr_front/navigation/form/_area_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return int|string
     */
    private function grrPeriodicityTypeName(int $type)
    {
        return PeriodicityConstant::getTypePeriodicite($type);
    }

    private function grrWeekNiceName(Week $week): string
    {
        return $this->twigEnvironment->render(
            '@grr_front/weekly/_nice_name.html.twig',
            ['week' => $week]
        );
    }

    private function grrLegendEntryType(Area $area): string
    {
        $types = $this->entryTypeRepository->findAll();

        return $this->twigEnvironment->render(
            '@grr_front/_legend_entry_type.html.twig',
            ['types' => $types]
        );
    }

    private function grrCompanyName(): string
    {
        $company = $this->settingRepository->getValueByName(SettingConstants::COMPANY);

        return $company ?? 'Grr';
    }
}
