<?php

namespace Grr\GrrBundle\Twig;

use Carbon\CarbonInterface;
use Grr\GrrBundle\Templating\Helper\RouterHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GrrhUrlHelperExtension extends AbstractExtension
{
    /**
     * @var RouterHelper
     */
    private $routerHelper;

    public function __construct(
        RouterHelper $routerHelper
    ) {
        $this->routerHelper = $routerHelper;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateRouteMonthView', function (int $year = null, int $month = null): string {
                    return $this->routerHelper->generateRouteMonthView($year, $month);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteWeekView', function (int $week): string {
                    return $this->routerHelper->generateRouteWeekView($week);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteDayView', function (int $day, CarbonInterface $date = null): string {
                    return $this->routerHelper->generateRouteDayView($day, $date);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteAddEntry',
                function (int $area, int $room, int $day, int $hour = null, int $minute = null): string {
                    return $this->routerHelper->generateRouteAddEntry($area, $room, $day, $hour, $minute);
                }
            ),
        ];
    }
}
