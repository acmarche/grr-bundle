<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'framework',
        [
            'default_locale' => 'fr',
            'enabled_locales' => ['en', 'fr'],
        ]
    );
};
