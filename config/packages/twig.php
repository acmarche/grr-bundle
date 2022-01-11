<?php

declare(strict_types=1);

use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'twig',
        [
            'form_themes' => [
                'bootstrap_5_layout.html.twig',
            ],
            'globals' => [
                'grr' => service(SettingRepositoryInterface::class),
            ],
        ]
    );

    $containerConfigurator->extension(
        'twig',
        [
            'paths' => [
                '%kernel.project_dir%/src/Grr/GrrBundle/templates/admin' => 'grr_admin',
                '%kernel.project_dir%/src/Grr/GrrBundle/templates/front' => 'grr_front',
                '%kernel.project_dir%/src/Grr/GrrBundle/templates/security' => 'grr_security',
                '%kernel.project_dir%/src/Grr/GrrBundle/templates/default' => 'grr_default',
                '%kernel.project_dir%/src/Grr/GrrBundle/public/images' => 'images',
                '%kernel.project_dir%/src/Grr/GrrBundle/public/css' => 'css',
            ],
        ]
    );
};
