<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 2/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Setting\Handler;

use Exception;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\SettingEntity;
use Grr\GrrBundle\Setting\Factory\SettingFactory;

class SettingHandler
{
    public function __construct(
        private readonly SettingFactory $settingFactory,
        private readonly SettingRepositoryInterface $settingRepository,
        private readonly SettingProvider $settingProvider
    ) {
    }

    public function handleEdit($data): void
    {
        foreach ($data as $name => $value) {
            $setting = $this->settingRepository->findOneBy([
                'name' => $name,
            ]);
            if (null === $setting) {
                $this->handleNewSetting($name, $value);
                continue;
            }
            $this->handleExistSetting($setting, $value);
            continue;
        }

        $this->settingRepository->flush();
    }

    protected function handleNewSetting(string $name, $value): void
    {
        if (null === $value) {
            return;
        }

        $value = $this->handleValue($name, $value);

        $setting = $this->settingFactory->createNew($name, $value);
        $this->settingRepository->persist($setting);
    }

    protected function handleExistSetting(SettingEntity $setting, $value): void
    {
        if (null === $value) {
            $this->settingRepository->remove($setting);

            return;
        }
        $name = $setting->getName();
        $value = $this->handleValue($name, $value);
        $setting->setValue($value);
    }

    protected function handleValue(string $name, $value)
    {
        try {
            $service = $this->settingProvider->loadInterfaceByKey($name);
            $value = $service->bindValue($value);
        } catch (Exception) {
        }

        return $value;
    }
}
