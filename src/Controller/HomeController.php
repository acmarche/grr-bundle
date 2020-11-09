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

use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\I18n\LocalHelper;
use Grr\GrrBundle\Navigation\RessourceSelectedHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var RessourceSelectedHelper
     */
    private $ressourceSelectedHelper;
    /**
     * @var LocalHelper
     */
    private $localHelper;
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;

    public function __construct(
        RessourceSelectedHelper $ressourceSelectedHelper,
        LocalHelper $localHelper,
        AreaRepositoryInterface $areaRepository
    ) {
        $this->ressourceSelectedHelper = $ressourceSelectedHelper;
        $this->localHelper = $localHelper;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route("/{_locale<%grr.supported_locales%>}", name="grr_homepage")
     */
    public function redirectToScheduler(): Response
    {
        $defaultLocal = $this->localHelper->getDefaultLocal();

        try {
            $area = $this->ressourceSelectedHelper->getArea();
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }

        $room = $this->ressourceSelectedHelper->getRoom();

        $params = ['_locale' => $defaultLocal, 'area' => $area->getId()];

        if (null !== $room) {
            $params['room'] = $room->getId();
        }

        $today = new \DateTime();

        return $this->redirectToRoute(
            'grr_front_view',
            [
                'area' => $area->getId(),
                'date' => $today->format('Y-m-d'),
                'view' => ViewInterface::VIEW_MONTHLY,
            ]
        );
    }

    /**
     * @Route("/vuejs", name="vuejs")
     */
    public function index()
    {
        return $this->render(
            '@Grr/vuejs/index.html.twig',
            [
                'areas' => $this->areaRepository->findAll(),
            ]
        );
    }
}
