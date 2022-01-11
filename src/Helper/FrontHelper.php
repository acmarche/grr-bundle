<?php
/**
 * This file is part of grr5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 27/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Helper;

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\Core\Setting\SettingConstants;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Twig\Environment;

class FrontHelper
{
    public function __construct(
        private Environment $environment,
        private TypeEntryRepositoryInterface $typeEntryRepository,
        private SettingRepositoryInterface $settingRepository
    ) {
    }

    public function periodicityTypeName(int $type): int|string
    {
        return PeriodicityConstant::getTypePeriodicite($type);
    }

    public function legendTypeEntry(?AreaInterface $area = null): string
    {
        $types = $this->typeEntryRepository->findByArea($area);

        return $this->environment->render(
            '@grr_front/_legend_entry_type.html.twig',
            [
                'types' => $types,
            ]
        );
    }

    public function companyName(): string
    {
        return $this->settingRepository->getValueByName(SettingConstants::COMPANY) ?? 'Grr';
    }
}
