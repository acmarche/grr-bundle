<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/03/19
 * Time: 17:35.
 */

namespace Grr\GrrBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\InvalidParameterException;


class AjaxController extends AbstractController
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly EntryRepository $entryRepository,
        private readonly AuthorizationHelper $authorizationHelper
    ) {
    }

    #[Route(path: '/ajax/getrooms', name: 'grr_ajax_getrooms')]
    public function ajaxRequestGetRooms(Request $request): Response
    {
        $areaId = (int) $request->get('id');
        $required = filter_var($request->get('isRequired'), FILTER_VALIDATE_BOOLEAN, false);
        $restricted = filter_var($request->get('isRestricted'), FILTER_VALIDATE_BOOLEAN, false);
        $area = $this->areaRepository->find($areaId);
        if (null === $area) {
            throw new InvalidParameterException('Area not found');
        }

        if (! $restricted) {
            $rooms = $this->roomRepository->findByArea($area);
        } else {
            $user = $this->getUser();
            if (!$user instanceof UserInterface) {
                throw new InvalidParameterException('You must be login');
            }

            $rooms = $this->authorizationHelper->getRoomsUserCanAdd($user, $area);
        }

        return $this->render('@Grr/ajax/_rooms_options.html.twig', [
            'rooms' => $rooms,
            'required' => $required,
        ]);
    }

    #[Route(path: '/ajax/getentries', name: 'grr_ajax_getentries')]
    public function ajaxRequestGetEntries(Request $request): Response
    {
        $data = json_decode($request->getContent(), null, 512, JSON_THROW_ON_ERROR);
        $areaId = (int) $data->area;
        $roomId = (int) $data->room;
        $date = DateTime::createFromFormat('Y-m-d', $data->date);
        $area = $this->areaRepository->find($areaId);
        $args = [
            'dateStart' => $date,
            'area' => $area,
        ];
        if (0 !== $roomId) {
            $room = $this->roomRepository->find($roomId);
            $args['room'] = $room;
        }

        $entries = $this->entryRepository->search($args);

        return $this->render(
            '@grr_front/view/monthly/_list_ajax.html.twig',
            [
                'entries' => $entries,
            ]
        );
    }
}
