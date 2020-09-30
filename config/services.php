<?php

use Grr\Core\Setting\General\SettingGeneralInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Notification\BrowserGrrChannel;
use Grr\GrrBundle\Security\Voter\CriterionInterface;
use Grr\GrrBundle\Security\Voter\PostVoter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('grr.supported_locales', ['fr', 'en', 'nl']);

    $services = $containerConfigurator->services();

    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services = $services->instanceof(CriterionInterface::class)
        ->tag('entry.voter');

    $services = $services->instanceof(SettingGeneralInterface::class)
        ->tag('grr.setting');

    $services = $services->set(BrowserGrrChannel::class)
        ->tag(
            'notifier.channel',
            [
                'channel' => 'browsergrr',
            ]
        );

    $services
        ->load('Grr\GrrBundle\\', __DIR__.'/../src/*')
        ->exclude([__DIR__.'/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}']);

    $services->load('Grr\Core\\', __DIR__.'/../../Core')
        ->exclude([__DIR__.'/../../Core/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}']);

    /*   $services->set(ModuleSender::class)
           ->arg('$modules', tagged_iterator('grr.module'));*/

    $services->set(SettingProvider::class)
        ->args(
            [
                tagged_iterator('grr.setting', 'getDefaultIndexName'),
                tagged_locator('grr.setting', 'key', 'getDefaultIndexName'),
            ]
        );

    $services->set(PostVoter::class)
        ->args([tagged_locator('entry.voter')]);
};
