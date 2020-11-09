<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Authorization\Message\AuthorizationCreated;
use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\GrrBundle\Authorization\Manager\AuthorizationManager;
use Grr\GrrBundle\Authorization\Repository\AuthorizationRepository;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Security\Voter\AreaVoter;
use Grr\GrrBundle\Security\Voter\RoomVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/authorization")
 */
class AuthorizationController extends AbstractController
{
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;
    /**
     * @var AuthorizationManager
     */
    private $authorizationManager;

    public function __construct(
        AuthorizationManager $authorizationManager,
        AuthorizationRepositoryInterface $authorizationRepository
    ) {
        $this->authorizationRepository = $authorizationRepository;
        $this->authorizationManager = $authorizationManager;
    }

    /**
     * @Route("/delete", name="grr_authorization_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('idauth');
        $token = $request->get('_tokenauth');
        $urlBack = $request->get('_urlback', '/');

        $authorization = $this->authorizationRepository->find($id);

        if (null === $authorization) {
            $this->createNotFoundException();
        }

        if (null !== ($area = $authorization->getArea())) {
            $this->denyAccessUnlessGranted(AreaVoter::EDIT, $area);
        }

        if (null !== ($room = $authorization->getRoom())) {
            $this->denyAccessUnlessGranted(RoomVoter::EDIT, $room);
        }

        if ($this->isCsrfTokenValid('delete'.$authorization->getId(), $token)) {
            $this->authorizationManager->remove($authorization);
            $this->authorizationManager->flush();

            $this->dispatchMessage(new AuthorizationCreated($room->getId()));
        } else {
            $this->addFlash('danger', 'authorization.flash.model.delete.error');
        }

        return $this->redirect($urlBack);
    }

    /**
     * @Route("/room/{id}", name="grr_authorization_show_by_room", methods={"GET"})
     * @IsGranted("grr.room.edit", subject="room")
     */
    public function show(Room $room): Response
    {
        $authorizations = $this->authorizationRepository->findByRoom($room);
        $urlBack = $this->generateUrl('grr_authorization_show_by_user', ['id' => $room->getId()]);

        return $this->render(
            '@grr_security/authorization/room/show.html.twig',
            [
                'room' => $room,
                'authorizations' => $authorizations,
                'url_back' => $urlBack,
            ]
        );
    }
}
