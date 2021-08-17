<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Authorization\Form\AuthorizationUserType;
use Grr\GrrBundle\Authorization\Handler\HandlerAuthorization;
use Grr\GrrBundle\Entity\Security\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/authorization/user")
 * @IsGranted("ROLE_GRR_MANAGER_USER")
 */
class AuthorizationUserController extends AbstractController
{
    private HandlerAuthorization $handlerAuthorization;
    private AuthorizationRepositoryInterface $authorizationRepository;

    public function __construct(
        HandlerAuthorization $handlerAuthorization,
        AuthorizationRepositoryInterface $authorizationRepository
    ) {
        $this->handlerAuthorization = $handlerAuthorization;
        $this->authorizationRepository = $authorizationRepository;
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
            '@grr_admin/authorization/user/new.html.twig',
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
            '@grr_admin/authorization/user/show.html.twig',
            [
                'user' => $user,
                'authorizations' => $authorizations,
                'url_back' => $urlBack,
            ]
        );
    }
}
