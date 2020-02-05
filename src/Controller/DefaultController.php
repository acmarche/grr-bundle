<?php
/**
 * This file is part of sf5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 16/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Controller;

use Grr\Core\Factory\CarbonFactory;
use Grr\GrrBundle\Navigation\RessourceSelectedHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var CarbonFactory
     */
    private $carbonFactory;
    /**
     * @var RessourceSelectedHelper
     */
    private $ressourceSelectedHelper;

    public function __construct(CarbonFactory $carbonFactory, RessourceSelectedHelper $ressourceSelectedHelper)
    {
        $this->carbonFactory = $carbonFactory;
        $this->ressourceSelectedHelper = $ressourceSelectedHelper;
    }

    /**
     * @Route("/", name="grr_homepage")
     */
    public function home()
    {
        $today = $this->carbonFactory->getToday();

        try {
            $area = $this->ressourceSelectedHelper->getArea();
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }

        $room = $this->ressourceSelectedHelper->getRoom();

        $params = ['area' => $area->getId(), 'year' => $today->year, 'month' => $today->month];

        if (null !== $room) {
            $params['room'] = $room->getId();
        }

        return $this->redirectToRoute(
            'grr_front_month',
            $params
        );
    }
}
