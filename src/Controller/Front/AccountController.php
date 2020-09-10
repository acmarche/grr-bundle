<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\Core\Password\Events\PasswordEventUpdated;
use Grr\Core\Security\PasswordHelper;
use Grr\Core\User\Events\UserEventDeleted;
use Grr\Core\User\Events\UserEventUpdated;
use Grr\GrrBundle\Authorization\Repository\AuthorizationRepository;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Grr\GrrBundle\User\Form\UserFrontType;
use Grr\GrrBundle\User\Form\UserPasswordType;
use Grr\GrrBundle\User\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 * @IsGranted("ROLE_GRR")
 */
class AccountController extends AbstractController
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
    /**
     * @var AuthorizationRepository
     */
    private $authorizationRepository;
    /**
     * @var EmailPreferenceRepository
     */
    private $emailPreferenceRepository;

    public function __construct(
        UserManager $userManager,
        PasswordHelper $passwordHelper,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationRepositoryInterface $authorizationRepository,
        EmailPreferenceRepository $emailPreferenceRepository
    ) {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->passwordHelper = $passwordHelper;
        $this->authorizationRepository = $authorizationRepository;
        $this->emailPreferenceRepository = $emailPreferenceRepository;
    }

    /**
     * @Route("/show", name="grr_account_show", methods={"GET"})
     */
    public function show(): Response
    {
        /**
         * @var UserInterface $user
         */
        $user = $this->getUser();
        $authorizations = $this->authorizationRepository->findByUser($user);
        $preferences = $this->emailPreferenceRepository->findOneByUser($user);

        return $this->render(
            '@grr_front/account/show.html.twig',
            [
                'user' => $user,
                'authorizations' => $authorizations,
                'preference'=>$preferences
            ]
        );
    }

    /**
     * @Route("/edit", name="grr_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFrontType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new UserEventUpdated($user));

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

            $user->setPassword($this->passwordHelper->encodePassword($user, $password));

            $this->userManager->flush();

            $this->eventDispatcher->dispatch(new PasswordEventUpdated($user));

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

            $this->eventDispatcher->dispatch(new UserEventDeleted($user));
        }

        return $this->redirectToRoute('grr_homepage');
    }
}
