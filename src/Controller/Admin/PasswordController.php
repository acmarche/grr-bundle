<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Password\Events\PasswordEventUpdated;
use Grr\Core\Security\PasswordHelper;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\User\Form\UserPasswordType;
use Grr\GrrBundle\User\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/password")
 * @IsGranted("ROLE_GRR_MANAGER_USER")
 */
class PasswordController extends AbstractController
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var PasswordHelper
     */
    private $passwordHelper;

    public function __construct(
        UserManager $userManager,
        PasswordHelper $passwordHelper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->passwordHelper = $passwordHelper;
    }

    /**
     * @Route("/{id}", name="grr_admin_user_password")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data->getPassword();
            $user->setPassword($this->passwordHelper->encodePassword($user, $password));
            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new PasswordEventUpdated($user));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                ['id' => $user->getId()]
            );
        }

        return $this->render(
            '@grr_admin/user/edit_password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
