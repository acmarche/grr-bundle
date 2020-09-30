<?php

use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\Authenticator\GrrAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'security',
        [
            'encoders' => [
                User::class => [
                    'algorithm' => 'auto',
                ],
            ],
        ]
    );

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

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => [
                    'guard' => ['authenticators' => [GrrAuthenticator::class]],
                    'logout' => ['path' => 'app_logout'],
                ],
            ],
        ]
    );

    $containerConfigurator->extension(
        'security',
        [
            'role_hierarchy' => [
                'ROLE_GRR_ADMINISTRATOR' => ['ROLE_GRR', 'ROLE_GRR_MANAGER_USER'],
                'ROLE_GRR_ACTIVE_USER' => ['ROLE_GRR'],
                'ROLE_GRR_MANAGER_USER' => ['ROLE_GRR'],
            ],
        ]
    );
};
