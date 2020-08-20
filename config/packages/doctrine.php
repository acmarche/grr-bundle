<?php

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\Core\Contrat\Entity\Security\AuthorizationInterface;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\Contrat\Entity\TypeEntryInterface;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\Authorization;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Entity\TypeEntry;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'Grr\GrrBundle' => [
                        'is_bundle' => false,
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/src/Grr/GrrBundle/src/Entity',
                        'prefix' => 'Grr\GrrBundle',
                        'alias' => 'GrrGrrBundle',
                    ],
                ],
                'resolve_target_entities' => [
                    AreaInterface::class => Area::class,
                    RoomInterface::class => Room::class,
                    EntryInterface::class => Entry::class,
                    TypeEntryInterface::class => TypeEntry::class,
                    PeriodicityInterface::class => Periodicity::class,
                    UserInterface::class => User::class,
                    AuthorizationInterface::class => Authorization::class,
                ],
            ],
        ]
    );
};
