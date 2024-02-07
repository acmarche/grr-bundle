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

use Grr\Core\Contrat\Entity\RoomInterface;
use DateTime;
use Exception;
use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\I18n\LocalHelper;
use Grr\GrrBundle\Navigation\RessourceSelectedHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController
{
    public function __construct(
        private readonly RessourceSelectedHelper $ressourceSelectedHelper,
        private readonly LocalHelper $localHelper,
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    #[Route(path: '/{_locale<%grr.supported_locales%>}', name: 'grr_homepage')]
    public function redirectToScheduler(): Response
    {
        $defaultLocal = $this->localHelper->getDefaultLocal();
        try {
            $area = $this->ressourceSelectedHelper->getArea();
        } catch (Exception $exception) {
            return new Response($exception->getMessage());
        }

        $room = $this->ressourceSelectedHelper->getRoom();
        $today = new DateTime();
        $params = [
            '_locale' => $defaultLocal,
            'area' => $area->getId(),
            'view' => ViewInterface::VIEW_MONTHLY,
            'date' => $today->format('Y-m-d'),
        ];
        if ($room instanceof RoomInterface) {
            $params['room'] = $room->getId();
        }

        return $this->redirectToRoute(
            'grr_front_view',
            $params
        );
    }

    #[Route(path: '/vuejs', name: 'vuejs')]
    public function index(): Response
    {
        return $this->render(
            '@Grr/vuejs/index.html.twig',
            [
                'areas' => $this->areaRepository->findAll(),
            ]
        );
    }
}
