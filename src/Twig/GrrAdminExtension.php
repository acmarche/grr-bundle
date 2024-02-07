<?php

namespace Grr\GrrBundle\Twig;

use Grr\Core\Provider\DateProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GrrAdminExtension extends AbstractExtension
{
    public function __construct(
        private readonly DateProvider $dateProvider
    ) {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrWeekDaysName',
                fn ($value): string => $this->weekDaysName($value)
            ),
            new TwigFilter(
                'grrDisplayColor',
                fn (string $value): string => $this->displayColor($value),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function weekDaysName(string $value): string
    {
        $jours = $this->dateProvider->weekDaysName();

        return $jours[$value] ?? $value;
    }

    public function displayColor(string $value): string
    {
        return '<span style="background-color: '.$value.';"></span>';
    }
}
