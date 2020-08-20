<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('grr.supported_locales', ['fr', 'en', 'nl']);

    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->load('Grr\GrrBundle\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}']);

    $services->load('Grr\Core\\', __DIR__.'/../../Core')
        ->exclude([__DIR__.'/../../Core/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}']);

    /**
     * populaite var construct $modules
     */
    /*   $services->set(ModuleSender::class)
           ->arg('$modules', tagged_iterator('grr.module'));
    */
};
