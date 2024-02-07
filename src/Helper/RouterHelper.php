<?php
/**
 * This file is part of grr5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 27/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Helper;

use DateTime;
use DateTimeInterface;
use Grr\Core\Contrat\Front\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class RouterHelper
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router
    ) {
    }

    public function generateRouteView(?DateTimeInterface $dateSelected = null, ?string $viewSelected = null): string
    {
        $request = $this->requestStack->getMainRequest();
        if (! $request instanceof Request) {
            return '';
        }

        $attributes = $request->attributes->get('_route_params');

        $area = $attributes['area'] ?? 0;
        $room = $attributes['room'] ?? 0;

        if (!$dateSelected instanceof DateTimeInterface) {
            $dateSelected = new DateTime();
        }

        if (! $viewSelected) {
            $viewSelected = ViewInterface::VIEW_MONTHLY;
        }

        $params = [
            'area' => $area,
            'date' => $dateSelected->format('Y-m-d'),
            'view' => $viewSelected,
        ];

        if ($room) {
            $params['room'] = $room;
        }

        return $this->router->generate('grr_front_view', $params);
    }

    public function generateRouteAddEntry(
        int $area,
        int $room,
        DateTimeInterface $dateSelected,
        ?int $hour,
        ?int $minute
    ): string {
        $request = $this->requestStack->getMainRequest();
        if (! $request instanceof Request) {
            return '';
        }

        $hour ??= 0;
        $minute ??= 0;

        $params = [
            'area' => $area,
            'room' => $room,
            'date' => $dateSelected->format('Y-m-d'),
            'hour' => $hour,
            'minute' => $minute,
        ];

        return $this->router->generate('grr_front_entry_new', $params);
    }
}
