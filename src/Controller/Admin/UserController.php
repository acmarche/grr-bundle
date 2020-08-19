<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Security\PasswordHelper;
use Grr\Core\User\Events\UserEventCreated;
use Grr\Core\User\Events\UserEventDeleted;
use Grr\Core\User\Events\UserEventUpdated;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\User\Factory\UserFactory;
use Grr\GrrBundle\User\Form\SearchUserType;
use Grr\GrrBundle\User\Form\UserAdvanceType;
use Grr\GrrBundle\User\Form\UserNewType;
use Grr\GrrBundle\User\Form\UserRoleType;
use Grr\GrrBundle\User\Manager\UserManager;
use Grr\GrrBundle\User\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 * @IsGranted("ROLE_GRR_MANAGER_USER")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $utilisateurRepository;

    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var PasswordHelper
     */
    private $passwordEncoder;

    public function __construct(
        UserRepository $utilisateurRepository,
        UserFactory $userFactory,
        UserManager $userManager,
        PasswordHelper $passwordEncoder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->userManager = $userManager;
        $this->userFactory = $userFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="grr_admin_user_index", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $args = $users = [];
        $form = $this->createForm(SearchUserType::class, $args);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
        }

        $users = $this->utilisateurRepository->search($args);

        return $this->render(
            '@grr_admin/user/index.html.twig',
            [
                'users' => $users,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new", name="grr_admin_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->userFactory->createNew();
        $form = $this->createForm(UserNewType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $this->userManager->insert($user);

            $this->eventDispatcher->dispatch(new UserEventCreated($user));

            return $this->redirectToRoute('grr_admin_user_roles', ['id' => $user->getId()]);
        }

        return $this->render(
            '@grr_admin/user/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_user_show", methods={"GET"})
     */
    public function show(User $utilisateur): Response
    {
        return $this->render(
            '@grr_admin/user/show.html.twig',
            [
                'user' => $utilisateur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="grr_admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserAdvanceType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new UserEventUpdated($user));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                ['id' => $user->getId()]
            );
        }

        return $this->render(
            '@grr_admin/user/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Displays a form to edit an existing User utilisateur.
     *
     * @Route("/{id}/roles", name="grr_admin_user_roles", methods={"GET","POST"})
     */
    public function roles(Request $request, User $user)
    {
        $form = $this->createForm(UserRoleType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new UserEventUpdated($user));

            return $this->redirectToRoute(
                'grr_admin_user_show',
                ['id' => $user->getId()]
            );
        }

        return $this->render(
            '@grr_admin/user/roles.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getEmail(), $request->request->get('_token'))) {
            $this->userManager->remove($user);
            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new UserEventDeleted($user));
        }

        return $this->redirectToRoute('grr_admin_user_index');
    }
}
