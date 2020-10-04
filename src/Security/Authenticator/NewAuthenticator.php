<?php

namespace Grr\GrrBundle\Security\Authenticator;

use Symfony\Component\Ldap\Security\LdapBadge;
use function gettype;
use function is_object;
use function is_string;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use function strlen;

/**
 * @see FormLoginAuthenticator
 */
class NewAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private $httpUtils;
    private $userProvider;

    public function __construct(
        HttpUtils $httpUtils,
        UserProviderInterface $userProvider
    ) {
        $this->httpUtils = $httpUtils;
        $this->userProvider = $userProvider;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, 'app_login');
    }

    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $this->getCredentials($request);
        $user = $this->userProvider->loadUserByUsername($credentials['username']);
        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
        }

        $passport = new Passport($user, new PasswordCredentials($credentials['password']), [new RememberMeBadge()]);
        $passport->addBadge(new CsrfTokenBadge('authenticate', $credentials['csrf_token']));

        if ($this->userProvider instanceof PasswordUpgraderInterface) {
            $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
        }

     //   $passport->addBadge(new LdapBadge('jflsap', 'modn', 'searchdn', 'searchpwd', 'myquey'));

        return $passport;
    }

    /**
     * @param PassportInterface $passport
     * @param string $firewallName
     * @return TokenInterface
     */
    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        return new UsernamePasswordToken($passport->getUser(), null, $firewallName, $passport->getUser()->getRoles());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->httpUtils->generateUri($request, 'grr_homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        $url = $this->getLoginUrl($request);

        return new RedirectResponse($url);
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        if (!is_string($credentials['username']) && (!is_object($credentials['username']) || !method_exists(
                    $credentials['username'],
                    '__toString'
                ))) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be a string, "%s" given.', 'username', gettype($credentials['username'])));
        }

        $credentials['username'] = trim($credentials['username']);

        if (strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }
}
