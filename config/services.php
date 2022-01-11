<?php

use Grr\Core\Contrat\Front\ViewInterface;
use Grr\Core\Contrat\Modules\GrrModuleInterface;
use Grr\Core\Setting\General\SettingGeneralInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\Core\View\ViewLocator;
use Grr\GrrBundle\Notification\BrowserGrrChannel;
use Grr\GrrBundle\Parameter\Option;
use Grr\GrrBundle\Security\Ldap\LdapGrr;
use Grr\GrrBundle\Security\Voter\CriterionInterface;
use Grr\GrrBundle\Security\Voter\PostVoter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('grr.supported_locales', ['fr', 'en', 'nl']);
    $parameters->set(Option::LDAP_DN, '%env(ACLDAP_DN)%');
    $parameters->set(Option::LDAP_USER, '%env(ACLDAP_USER)%');
    $parameters->set(Option::LDAP_PASSWORD, '%env(ACLDAP_PASSWORD)%');

    $services = $containerConfigurator->services();

    $services = $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services = $services->instanceof(CriterionInterface::class)
        ->tag('entry.voter');

    $services = $services->instanceof(SettingGeneralInterface::class)
        ->tag('grr.setting');

    $services = $services->instanceof(ViewInterface::class)
        ->tag('grr.render');

    $services = $services->instanceof(GrrModuleInterface::class)
            ->tag('grr.module');

    $services = $services->set(BrowserGrrChannel::class)
        ->tag(
            'notifier.channel',
            [
                'channel' => 'browsergrr',
            ]
        );

    $services = $services->set('notifier.channel.browser', BrowserGrrChannel::class)
        ->args([service('request_stack')])
        ->tag('notifier.channel', [
            'channel' => 'browsergrr',
        ]);

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

    $services->set(ViewLocator::class)
        ->args(
            [
                tagged_iterator('grr.render', 'getDefaultIndexName'),
                tagged_locator('grr.render', 'key', 'getDefaultIndexName'),
            ]
        );

    $services->set(PostVoter::class)
        ->args([tagged_locator('entry.voter')]);

    if (interface_exists(LdapInterface::class)) {
        $services
            ->set(Ldap::class)
            ->args(['@Symfony\Component\Ldap\Adapter\ExtLdap\Adapter'])
            ->tag('ldap');
        $services->set(Adapter::class)
            ->args(
                [
                    [
                        'host' => '%env(ACLDAP_URL)%',
                        'port' => 636,
                        'encryption' => 'ssl',
                        'options' => [
                            'protocol_version' => 3,
                            'referrals' => false,
                        ],
                    ],
                ]
            );

        $services->set(LdapGrr::class)
            ->arg('$adapter', service(Adapter::class))
            ->tag('ldap'); //necessary for new LdapBadge(LdapGrr::class)
    }
};
