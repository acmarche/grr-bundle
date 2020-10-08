<?php

namespace Grr\GrrBundle\Twig;

use Grr\Core\Provider\DateProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GrrAdminExtension extends AbstractExtension
{
    /**
     * @var DateProvider
     */
    private $dateProvider;

    public function __construct(DateProvider $dateProvider)
    {
        $this->dateProvider = $dateProvider;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrWeekDaysName', function ($value): string {
                    return $this->weekDaysName($value);
                }
            ),
            new TwigFilter(
                'grrDisplayColor', function (string $value): string {
                    return $this->displayColor($value);
                }, ['is_safe' => ['html']]
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
