<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/03/19
 * Time: 17:35.
 */

namespace Grr\GrrBundle\Controller;

use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Class AjaxController.
 *
 * @IsGranted("ROLE_GRR")
 */
class AjaxController extends AbstractController
{
    private AreaRepositoryInterface $areaRepository;
    private RoomRepositoryInterface $roomRepository;
    private AuthorizationHelper $authorizationHelper;

    public function __construct(
        AreaRepositoryInterface $areaRepository,
        RoomRepositoryInterface $roomRepository,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @Route("/ajax/getrooms", name="grr_ajax_getrooms")
     */
    public function ajaxRequestGetRooms(Request $request): Response
    {
        $areaId = (int) $request->get('id');
        $required = filter_var($request->get('isRequired'), FILTER_VALIDATE_BOOLEAN, false);
        $restricted = filter_var($request->get('isRestricted'), FILTER_VALIDATE_BOOLEAN, false);

        $area = $this->areaRepository->find($areaId);
        if (null === $area) {
            throw new InvalidParameterException('Area not found');
        }

        if (!$restricted) {
            $rooms = $this->roomRepository->findByArea($area);
        } else {
            $user = $this->getUser();
            if (!$user) {
                throw new InvalidParameterException('You must be login');
            }
            $rooms = $this->authorizationHelper->getRoomsUserCanAdd($user, $area);
        }

        return $this->render('@Grr/ajax/_rooms_options.html.twig', ['rooms' => $rooms, 'required' => $required]);
    }
}
