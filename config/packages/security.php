<?php

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\Authenticator\GrrAuthenticator;
use Grr\GrrBundle\Security\Authenticator\GrrLdapAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => [
                'algorithm' => 'auto',
            ],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'grr_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [GrrAuthenticator::class];

    $main = [
        'provider' => 'grr_user_provider',
        'logout' => [
            'path' => 'app_logout',
        ],
        'form_login' => [],
        'entry_point' => GrrAuthenticator::class,
    ];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = GrrLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'role_hierarchy' => [
                SecurityRole::ROLE_GRR_ADMINISTRATOR => [
                    SecurityRole::ROLE_GRR,
                    SecurityRole::ROLE_GRR_MANAGER_USER,
                    SecurityRole::ROLE_GRR_BOOKING,
                ],
                SecurityRole::ROLE_GRR_ACTIVE_USER => [SecurityRole::ROLE_GRR],
                SecurityRole::ROLE_GRR_MANAGER_USER => [SecurityRole::ROLE_GRR],
                SecurityRole::ROLE_GRR_BOOKING => [SecurityRole::ROLE_GRR],
            ],
        ]
    );
};
