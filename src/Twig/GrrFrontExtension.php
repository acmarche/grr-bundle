<?php

namespace Grr\GrrBundle\Twig;

use Grr\Core\Model\Day;
use Grr\Core\Model\RoomModel;
use Grr\Core\Model\TimeSlot;
use Grr\Core\Model\Week;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Templating\Helper\FrontHelper;
use Grr\GrrBundle\Templating\Helper\NavigationHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GrrFrontExtension extends AbstractExtension
{
    /**
     * @var FrontHelper
     */
    private $frontHelper;
    /**
     * @var NavigationHelper
     */
    private $navigationHelper;

    public function __construct(
        FrontHelper $frontHelper,
        NavigationHelper $navigationHelper
    ) {

        $this->frontHelper = $frontHelper;
        $this->navigationHelper = $navigationHelper;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrPeriodicityTypeName', function (int $type) {
                return $this->frontHelper->grrPeriodicityTypeName($type);
            }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFilter(
                'grrWeekNiceName', function (Week $week): string {
                return $this->frontHelper->grrWeekNiceName($week);
            }, [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     *
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrMonthNavigationRender', function () :string {
                return $this->navigationHelper->monthNavigationRender();
            }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrMenuNavigationRender', function () :string {
                return $this->navigationHelper->menuNavigationRender();
            }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrGenerateCellDataDay', function (TimeSlot $hour, RoomModel $roomModel, Day $day): string {
                return $this->frontHelper->grrGenerateCellDataDay($hour, $roomModel, $day);
            }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrLegendEntryType', function (Area $area): string {
                return $this->frontHelper->grrLegendEntryType($area);
            }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrCompanyName', function (): string {
                return $this->frontHelper->grrCompanyName();
            }
            ),
        ];
    }


}
