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

use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\SettingEntity;
use Grr\GrrBundle\Setting\Factory\SettingFactory;
use Grr\GrrBundle\Setting\Manager\SettingManager;
use Grr\GrrBundle\Setting\Repository\SettingRepository;

class SettingHandler
{
    /**
     * @var SettingFactory
     */
    private $settingFactory;
    /**
     * @var SettingRepository
     */
    private $settingRepository;
    /**
     * @var SettingManager
     */
    private $settingManager;
    /**
     * @var SettingProvider
     */
    private $settingProvider;

    public function __construct(
        SettingFactory $settingFactory,
        SettingRepositoryInterface $settingRepository,
        SettingManager $settingManager,
        SettingProvider $settingProvider
    ) {
        $this->settingFactory = $settingFactory;
        $this->settingRepository = $settingRepository;
        $this->settingManager = $settingManager;
        $this->settingProvider = $settingProvider;
    }

    public function handleEdit($data): void
    {
        foreach ($data as $name => $value) {
            $setting = $this->settingRepository->findOneBy(['name' => $name]);
            if (null === $setting) {
                $this->handleNewSetting($name, $value);
                continue;
            }
            $this->handleExistSetting($setting, $value);
            continue;
        }

        $this->settingManager->flush();
    }

    protected function handleNewSetting(string $name, $value): void
    {
        if (null === $value) {
            return;
        }

        $value = $this->handleValue($name, $value);

        $setting = $this->settingFactory->createNew($name, $value);
        $this->settingManager->persist($setting);
    }

    protected function handleExistSetting(SettingEntity $setting, $value): void
    {
        if (null === $value) {
            $this->settingManager->remove($setting);

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
        } catch (\Exception $exception) {
        }

        return $value;
    }
}
