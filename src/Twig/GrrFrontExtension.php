<?php

namespace Grr\GrrBundle\Twig;

use Grr\GrrBundle\Navigation\AreaSelector;
use Grr\GrrBundle\Navigation\DateSelectorRender;
use Grr\GrrBundle\Helper\FrontHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GrrFrontExtension extends AbstractExtension
{
    /**
     * @var FrontHelper
     */
    private $frontHelper;
    /**
     * @var DateSelectorRender
     */
    private $dateSelectorRender;
    /**
     * @var AreaSelector
     */
    private $areaSelector;

    public function __construct(
        FrontHelper $frontHelper,
        DateSelectorRender $dateSelectorRender,
        AreaSelector $areaSelector
    ) {
        $this->frontHelper = $frontHelper;
        $this->dateSelectorRender = $dateSelectorRender;
        $this->areaSelector = $areaSelector;
    }

    /**
     * @return \Twig\TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'grrPeriodicityTypeName', function (int $type) {
                    return $this->frontHelper->periodicityTypeName($type);
                }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFilter(
                'grrBoolToArrow', [$this, 'grrBoolToArrow'], [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'grrDateSelector', function (): string {
                    return $this->dateSelectorRender->render();
                }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrAreaSelector', function (): string {
                    return $this->areaSelector->render();
                }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrLegendTypeEntry', function (): string {
                    return $this->frontHelper->legendTypeEntry();
                }, [
                    'is_safe' => ['html'],
                ]
            ),
            new TwigFunction(
                'grrCompanyName', function (): string {
                    return $this->frontHelper->companyName();
                }
            ),
        ];
    }

    public function grrBoolToArrow(bool $value): string
    {
        if (true === $value) {
            return '<i class="fas fa-chevron-down"></i>';
        }

        return '';
    }
}
