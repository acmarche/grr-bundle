<?php

namespace Grr\GrrBundle\Twig;

use Carbon\CarbonInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GrrhUrlHelperExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
        Environment $twigEnvironment,
        RouterInterface $router
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrGenerateRouteMonthView', function (int $year = null, int $month = null): string {
                    return $this->generateRouteMonthView($year, $month);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteWeekView', function (int $week): string {
                    return $this->generateRouteWeekView($week);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteDayView', function (int $day, CarbonInterface $date = null): string {
                    return $this->generateRouteDayView($day, $date);
                }
            ),
            new TwigFunction(
                'grrGenerateRouteAddEntry',
                function (int $area, int $room, int $day, int $hour = null, int $minute = null): string {
                    return $this->generateRouteAddEntry($area, $room, $day, $hour, $minute);
                }
            ),
        ];
    }

    public function generateRouteMonthView(int $year = null, int $month = null): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $area = $attributes['area'] ?? 0;
        $room = $attributes['room'] ?? 0;

        if (!$year) {
            $year = (int) $attributes['year'];
        }
        if (!$month) {
            $month = (int) $attributes['month'];
        }

        $params = ['area' => $area, 'year' => $year, 'month' => $month];

        if ($room) {
            $params['room'] = $room;
        }

        return $this->router->generate('grr_front_month', $params);
    }

    public function generateRouteWeekView(int $week): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $area = $attributes['area'] ?? 0;
        $room = $attributes['room'] ?? 0;
        $year = $attributes['year'] ?? 0;
        $month = $attributes['month'] ?? 0;

        $params = ['area' => $area, 'year' => $year, 'month' => $month, 'week' => $week];

        if ($room) {
            $params['room'] = (int) $room;
        }

        return $this->router->generate('grr_front_week', $params);
    }

    public function generateRouteDayView(int $day, CarbonInterface $date = null): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $area = $attributes['area'] ?? 0;
        $room = $attributes['room'] ?? 0;

        $year = $attributes['year'] ?? 0;
        $month = $attributes['month'] ?? 0;

        if ($date !== null) {
            $year = $date->year;
            $month = $date->month;
        }

        $params = ['area' => $area, 'year' => $year, 'month' => $month, 'day' => $day];

        if ($room) {
            $params['room'] = $room;
        }

        return $this->router->generate('grr_front_day', $params);
    }

    public function generateRouteAddEntry(int $area, int $room, int $day, int $hour = null, int $minute = null): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $year = $attributes['year'] ?? 0;
        $month = $attributes['month'] ?? 0;
        $hour = $hour ?? 0;
        $minute = $minute ?? 0;

        $params = [
            'area' => $area,
            'room' => $room,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'hour' => $hour,
            'minute' => $minute,
        ];

        return $this->router->generate('grr_front_entry_new', $params);
    }
}
