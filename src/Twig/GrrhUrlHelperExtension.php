<?php

namespace Grr\GrrBundle\Twig;

use DateTimeInterface;
use Grr\GrrBundle\Helper\RouterHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GrrhUrlHelperExtension extends AbstractExtension
{
    public function __construct(
        private RouterHelper $routerHelper
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateRouteView',
                fn (?DateTimeInterface $date = null, ?string $view = null): string => $this->routerHelper->generateRouteView($date, $view)
            ),
            new TwigFunction(
                'grrGenerateRouteAddEntry',
                fn (int $area, int $room, DateTimeInterface $date, int $hour = null, int $minute = null): string => $this->routerHelper->generateRouteAddEntry($area, $room, $date, $hour, $minute)
            ),
        ];
    }
}
