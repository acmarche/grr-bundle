<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\GrrBundle\Events\UserEvent;
use Grr\GrrBundle\Form\Security\UserPasswordType;
use Grr\GrrBundle\Form\Security\UserType;
use Grr\GrrBundle\Manager\UserManager;
use Grr\GrrBundle\Repository\Security\AuthorizationRepository;
use Grr\GrrBundle\Security\PasswordHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 * @IsGranted("ROLE_GRR")
 */
class AccountController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
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
    private $passwordEncoder;
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;

    public function __construct(
        UserManager $userManager,
        PasswordHelper $passwordEncoder,
        UserPasswordEncoderInterface $userPasswordEncoder,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationRepository $authorizationRepository
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationRepository = $authorizationRepository;
    }

    /**
     * @Route("/show", name="grr_account_show", methods={"GET"})
     */
    public function show(): Response
    {
        $user = $this->getUser();
        $authorizations = $this->authorizationRepository->findByUser($user);

        return $this->render(
            '@grr_front/account/show.html.twig',
            [
                'user' => $user,
                'authorizations' => $authorizations,
            ]
        );
    }

    /**
     * @Route("/edit", name="grr_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->flush();

            return $this->redirectToRoute('grr_account_show');
        }

        return $this->render(
            '@grr_front/account/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/password", name="grr_account_edit_password", methods={"GET", "POST"})
     */
    public function password(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data->getPassword();

            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

            $this->userManager->flush();

            $userEvent = new UserEvent($user);
            $this->eventDispatcher->dispatch($userEvent, UserEvent::CHANGE_PASSWORD_SUCCESS);

            return $this->redirectToRoute('grr_account_show');
        }

        return $this->render(
            '@grr_front/account/edit_password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/delete", name="grr_user_account_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userManager->remove($user);
            $this->userManager->flush();
        }

        return $this->redirectToRoute('grr_home');
    }
}
