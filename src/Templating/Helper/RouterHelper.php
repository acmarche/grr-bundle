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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class RouterHelper
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        RequestStack $requestStack,
        Environment $twigEnvironment,
        RouterInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->twigEnvironment = $twigEnvironment;
        $this->router = $router;
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

        if (null !== $date) {
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
