<?php

namespace Grr\GrrBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use Exception;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\SettingConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('grr_homepage');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $title = $this->settingRepository->getValueByName(SettingConstants::TITLE_HOME_PAGE);
        $message = $this->settingRepository->getValueByName(SettingConstants::MESSAGE_HOME_PAGE);
        $company = $this->settingRepository->getValueByName(SettingConstants::COMPANY);

        return $this->render(
            '@Grr/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'title' => $title,
                'message' => $message,
                'compagny' => $company,
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
