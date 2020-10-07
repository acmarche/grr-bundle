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

    public function generateRouteAddEntry(
        int $area,
        int $room,
        \DateTimeInterface $dateSelected,
        ?int $hour,
        ?int $minute
    ): string {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return '';
        }

        $hour = $hour ?? 0;
        $minute = $minute ?? 0;

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
