<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Password\PasswordHelper;
use Grr\Core\User\Message\UserCreated;
use Grr\Core\User\Message\UserDeleted;
use Grr\Core\User\Message\UserUpdated;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\User\Factory\UserFactory;
use Grr\GrrBundle\User\Form\SearchUserType;
use Grr\GrrBundle\User\Form\UserAdvanceType;
use Grr\GrrBundle\User\Form\UserNewType;
use Grr\GrrBundle\User\Form\UserRoleType;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;


#[Route(path: '/admin/user')]
#[IsGranted('ROLE_GRR_MANAGER_USER')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserFactory $userFactory,
        private readonly PasswordHelper $passwordHelper,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'grr_admin_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $args = [];
        $users = [];
        $form = $this->createForm(SearchUserType::class, $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }

        $users = $this->userRepository->search($args);
        $response = new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);

        return $this->render(
            '@grr_admin/user/index.html.twig',
            [
                'users' => $users,
                'form' => $form,
            ]
            , $response
        );
    }

    #[Route(path: '/new', name: 'grr_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->userFactory->createNew();
        $form = $this->createForm(UserNewType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHelper->encodePassword($user, $user->getPassword()));
            $this->userRepository->persist($user);
            $this->userRepository->flush();

            $this->messageBus->dispatch(new UserCreated($user->getId()));

            return $this->redirectToRoute('grr_admin_user_roles', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@grr_admin/user/new.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            '@grr_admin/user/show.html.twig',
            [
                'user' => $user,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'grr_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserAdvanceType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            $this->messageBus->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                [
                    'id' => $user->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }

    /**
     * Displays a form to edit an existing User utilisateur.
     */
    #[Route(path: '/{id}/roles', name: 'grr_admin_user_roles', methods: ['GET', 'POST'])]
    public function roles(Request $request, User $user): Response
    {
        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            $this->messageBus->dispatch(new UserUpdated($user->getId()));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                [
                    'id' => $user->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/user/roles.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getEmail(), $request->request->get('_token'))) {
            $id = $user->getId();
            $this->userRepository->remove($user);
            $this->userRepository->flush();

            $this->messageBus->dispatch(new UserDeleted($id));
        }

        return $this->redirectToRoute('grr_admin_user_index');
    }
}
