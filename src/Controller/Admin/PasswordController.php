<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Password\Message\PasswordUpdated;
use Grr\Core\Password\PasswordHelper;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\User\Form\UserPasswordType;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;


#[Route(path: '/admin/password')]
#[IsGranted('ROLE_GRR_MANAGER_USER')]
class PasswordController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHelper $passwordHelper,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/{id}', name: 'grr_admin_user_password')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data->getPassword();
            $user->setPassword($this->passwordHelper->encodePassword($user, $password));
            $this->userRepository->flush();

            $this->messageBus->dispatch(new PasswordUpdated($user->getId()));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                [
                    'id' => $user->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/user/edit_password.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }
}
