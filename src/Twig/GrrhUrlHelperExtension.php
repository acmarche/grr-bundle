<?php

namespace Grr\GrrBundle\Twig;

use DateTimeInterface;
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
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateRouteView', function (?DateTimeInterface $date = null, ?string $view = null): string {
                    return $this->routerHelper->generateRouteView($date, $view);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteAddEntry',
                function (
                    int $area,
                    int $room,
                    DateTimeInterface $date,
                    int $hour = null,
                    int $minute = null
                ): string {
                    return $this->routerHelper->generateRouteAddEntry($area, $room, $date, $hour, $minute);
                }
            ),
        ];
    }
}
