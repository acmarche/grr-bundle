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
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Class AjaxController.
 *
 *
 */
class AjaxController extends AbstractController
{
    private AreaRepositoryInterface $areaRepository;
    private RoomRepositoryInterface $roomRepository;
    private AuthorizationHelper $authorizationHelper;
    private EntryRepository $entryRepository;

    public function __construct(
        AreaRepositoryInterface $areaRepository,
        RoomRepositoryInterface $roomRepository,
        EntryRepository $entryRepository,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
        $this->authorizationHelper = $authorizationHelper;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @Route("/ajax/getrooms", name="grr_ajax_getrooms")
     */
    public function ajaxRequestGetRooms(Request $request): Response
    {
        $areaId = (int)$request->get('id');
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

    /**
     * @Route("/ajax/getentries", name="grr_ajax_getentries")
     */
    public function ajaxRequestGetEntries(Request $request): Response
    {
        $data = json_decode($request->getContent());

        $areaId = (int)$data->area;
        $roomId = (int)$data->room;
        $date = \DateTime::createFromFormat('Y-m-d', $data->date);

        $area = $this->areaRepository->find($areaId);
        $args = ['dateStart' => $date, 'area' => $area];
        if ($roomId) {
            $room = $this->roomRepository->find($roomId);
            $args['room'] = $room;
        }

        $entries = $this->entryRepository->search($args);

        return $this->render('@grr_front/view/monthly/_list_ajax.html.twig',
            ['entries' => $entries]
        );
    }
}
