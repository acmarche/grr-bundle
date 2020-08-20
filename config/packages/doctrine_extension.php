<?php

use DoctrineExtensions\Query\Mysql\Date;
use DoctrineExtensions\Query\Mysql\Day;
use DoctrineExtensions\Query\Mysql\Month;
use DoctrineExtensions\Query\Mysql\Year;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine',
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
