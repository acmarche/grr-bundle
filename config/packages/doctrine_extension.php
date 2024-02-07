<?php

use Grr\Core\Doctrine\Function\Date;
use Grr\Core\Doctrine\Function\Day;
use Grr\Core\Doctrine\Function\Month;
use Grr\Core\Doctrine\Function\Year;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'dql' => [
                    'string_functions' => [
                        'MONTH' => Month::class,
                        'YEAR' => Year::class,
                        'DAY' => Day::class,
                        'DATE' => Date::class,
                    ],
                ],
            ],
        ]
    );
};
