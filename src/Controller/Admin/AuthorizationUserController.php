<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Form\Security\AuthorizationUserType;
use Grr\GrrBundle\Manager\AuthorizationManager;
use Grr\GrrBundle\Model\AuthorizationModel;
use Grr\GrrBundle\Repository\Security\AuthorizationRepository;
use Grr\GrrBundle\Security\HandlerAuthorization;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/authorization/user")
 * @IsGranted("ROLE_GRR_MANAGER_USER")
 */
class AuthorizationUserController extends AbstractController
{
    /**
     * @var HandlerAuthorization
     */
    private $handlerAuthorization;
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var AuthorizationManager
     */
    private $authorizationManager;

    public function __construct(
        AuthorizationManager $authorizationManager,
        HandlerAuthorization $handlerAuthorization,
        AuthorizationRepository $authorizationRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->handlerAuthorization = $handlerAuthorization;
        $this->authorizationRepository = $authorizationRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationManager = $authorizationManager;
    }

    /**
     * @Route("/new/user/{id}", name="grr_authorization_from_user", methods={"GET", "POST"})
     */
    public function new(Request $request, User $user): Response
    {
        $authorizationModel = new AuthorizationModel();
        $authorizationModel->setUsers([$user]);

        $form = $this->createForm(AuthorizationUserType::class, $authorizationModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerAuthorization->handle($form);

            return $this->redirectToRoute('grr_authorization_show_by_user', ['id' => $user->getId()]);
        }

        return $this->render(
            '@grr_security/authorization/user/new.html.twig',
            [
                'authorizationArea' => $authorizationModel,
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_authorization_show_by_user", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $authorizations = $this->authorizationRepository->findByUser($user);
        $urlBack = $this->generateUrl('grr_authorization_show_by_user', ['id' => $user->getId()]);

        return $this->render(
            '@grr_security/authorization/user/show.html.twig',
            [
                'user' => $user,
                'authorizations' => $authorizations,
                'url_back' => $urlBack,
            ]
        );
    }
}
