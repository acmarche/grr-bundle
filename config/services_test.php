<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\EventListener\EnvelopeListener;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->set('mailer.dev.set_recipients', EnvelopeListener::class)
        ->tag('kernel.event_subscriber')
        ->arg('$sender', null)
        ->arg('$recipients', ['webmaster@marche.be']);
};
