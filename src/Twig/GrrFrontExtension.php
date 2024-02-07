<?php

namespace Grr\GrrBundle\Twig;

use Grr\GrrBundle\Helper\FrontHelper;
use Grr\GrrBundle\Navigation\AreaSelector;
use Grr\GrrBundle\Navigation\DateSelectorRender;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GrrFrontExtension extends AbstractExtension
{
    public function __construct(
        private readonly FrontHelper $frontHelper,
        private readonly DateSelectorRender $dateSelectorRender,
        private readonly AreaSelector $areaSelector
    ) {
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrPeriodicityTypeName',
                fn (int $type) => $this->frontHelper->periodicityTypeName($type),
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFilter(
                'grrBoolToArrow',
                fn (bool $value): string => $this->grrBoolToArrow($value),
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrDateSelector',
                fn (): string => $this->dateSelectorRender->render(),
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrAreaSelector',
                fn (): string => $this->areaSelector->render(),
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrLegendTypeEntry',
                fn (): string => $this->frontHelper->legendTypeEntry(),
                [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrCompanyName',
                fn (): string => $this->frontHelper->companyName()
            ),
        ];
    }

    public function grrBoolToArrow(bool $value): string
    {
        if ($value) {
            return '<i class="fas fa-chevron-down"></i>';
        }

        return '';
    }
}
