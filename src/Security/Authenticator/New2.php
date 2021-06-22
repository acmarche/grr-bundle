<?php

namespace Grr\GrrBundle\Security\Authenticator;

use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\HttpUtils;

class New2 extends AbstractLoginFormAuthenticator
{
    /**
     * @var HttpUtils
     */
    private $httpUtils;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
        $this->userRepository = $userRepository;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, 'app_login');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $csrf_token = $request->request->get('_csrf_token');

        $badge = new UserBadge(
            $username, function ($username) {
                return $this->userRepository->loadByUserNameOrEmail($username);
            }
        );

        $credentials = new PasswordCredentials($password);
        $badges = [
            new CsrfTokenBadge('authenticate', $csrf_token),
        ];

        return new Passport($badge, $credentials, $badges);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }
}
