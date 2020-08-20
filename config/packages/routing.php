<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'framework',
        ['router' => ['host' => 'example.org', 'scheme' => 'https', 'base_url' => 'my/path']]
    );
};
