<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Authorization\Form\AuthorizationUserType;
use Grr\GrrBundle\Authorization\Handler\HandlerAuthorization;
use Grr\GrrBundle\Authorization\Manager\AuthorizationManager;
use Grr\GrrBundle\Authorization\Repository\AuthorizationRepository;
use Grr\GrrBundle\Entity\Security\User;
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

    public function __construct(HandlerAuthorization $handlerAuthorization, \Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface $authorizationRepository)
    {
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
