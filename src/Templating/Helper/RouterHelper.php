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
use Grr\GrrBundle\Navigation\Navigation;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class RouterHelper
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function generateRouteView(?\DateTimeInterface $dateSelected = null, ?string $viewSelected = null): string
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $area = $attributes['area'] ?? 0;
        $room = $attributes['room'] ?? 0;

        if (!$dateSelected) {
            $dateSelected = new \DateTime();
        }

        if (!$viewSelected) {
            $viewSelected = Navigation::VIEW_MONTHLY;
        }

        $params = ['area' => $area, 'date' => $dateSelected->format('Y-m-d'), 'view' => $viewSelected];

        if ($room) {
            $params['room'] = $room;
        }

        return $this->router->generate('grr_front_view', $params);
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

    public function generateRouteDayView(int $day, CarbonInterface $carbon = null): string
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

        if (null !== $carbon) {
            $year = $carbon->year;
            $month = $carbon->month;
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
