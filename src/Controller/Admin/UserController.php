<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Events\UserEvent;
use Grr\Core\Security\PasswordHelper;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Form\Search\SearchUserType;
use Grr\GrrBundle\Form\Security\UserAdvanceType;
use Grr\GrrBundle\Form\Security\UserNewType;
use Grr\GrrBundle\Manager\UserManager;
use Grr\GrrBundle\Repository\Security\UserRepository;
use Grr\GrrBundle\Security\UserFactory;
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
        $utilisateur = $this->userFactory->createNew();
        $form = $this->createForm(UserNewType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setPassword($this->passwordEncoder->encodePassword($utilisateur, $utilisateur->getPassword()));
            $this->userManager->insert($utilisateur);

            $userEvent = new UserEvent($utilisateur);
            $this->eventDispatcher->dispatch($userEvent, UserEvent::NEW_SUCCESS);

            return $this->redirectToRoute('grr_admin_user_show', ['id' => $utilisateur->getId()]);
        }

        return $this->render(
            '@grr_admin/user/new.html.twig',
            [
                'user' => $utilisateur,
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
    public function edit(Request $request, User $utilisateur): Response
    {
        $form = $this->createForm(UserAdvanceType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->flush();

            $userEvent = new UserEvent($utilisateur);
            $this->eventDispatcher->dispatch($userEvent, UserEvent::EDIT_SUCCESS);

            return $this->redirectToRoute(
                'grr_admin_user_show',
                ['id' => $utilisateur->getId()]
            );
        }

        return $this->render(
            '@grr_admin/user/edit.html.twig',
            [
                'user' => $utilisateur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getEmail(), $request->request->get('_token'))) {
            $this->userManager->remove($utilisateur);
            $this->userManager->flush();

            $userEvent = new UserEvent($utilisateur);
            $this->eventDispatcher->dispatch($userEvent, UserEvent::DELETE_SUCCESS);
        }

        return $this->redirectToRoute('grr_admin_user_index');
    }
}
