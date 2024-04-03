<?php

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\Authenticator\GrrAuthenticator;
use Grr\GrrBundle\Security\Authenticator\GrrLdapAuthenticator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security) {

    $security
        ->provider('grr_user_provider')
        ->entity()
        ->class(User::class)
        ->property('username');

    // @see Symfony\Config\Security\FirewallConfig
    $main = [
        'provider' => 'grr_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => GrrAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
        'remember_me' => [
            'secret' => '%kernel.secret%',
            'lifetime' => 604800,
            'path' => '/',
            'always_remember_me' => true,
        ],
    ];

    $t =
        [
            SecurityRole::ROLE_GRR_ADMINISTRATOR => [
                SecurityRole::ROLE_GRR,
                SecurityRole::ROLE_GRR_MANAGER_USER,
                SecurityRole::ROLE_GRR_BOOKING,
            ],
            SecurityRole::ROLE_GRR_ACTIVE_USER => [SecurityRole::ROLE_GRR],
            SecurityRole::ROLE_GRR_MANAGER_USER => [SecurityRole::ROLE_GRR],
            SecurityRole::ROLE_GRR_BOOKING => [SecurityRole::ROLE_GRR],
        ];

    $authenticators = [GrrAuthenticator::class];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = GrrLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticators'] = $authenticators;
    $security
        ->roleHierarchy(SecurityRole::ROLE_GRR_ADMINISTRATOR, [
            SecurityRole::ROLE_GRR,
            SecurityRole::ROLE_GRR_MANAGER_USER,
            SecurityRole::ROLE_GRR_BOOKING,
        ])
        ->roleHierarchy(SecurityRole::ROLE_GRR_ACTIVE_USER, [SecurityRole::ROLE_GRR])
        ->roleHierarchy(SecurityRole::ROLE_GRR_MANAGER_USER, [SecurityRole::ROLE_GRR])
        ->roleHierarchy(SecurityRole::ROLE_GRR_BOOKING, [SecurityRole::ROLE_GRR])
        ->firewall('main', $main);
};

