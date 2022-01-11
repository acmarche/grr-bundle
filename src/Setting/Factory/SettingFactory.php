<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 17:42.
 */

namespace Grr\GrrBundle\Setting\Factory;

use Grr\GrrBundle\Entity\SettingEntity;

class SettingFactory
{
    public function createNew(string $name, array|string $value): SettingEntity
    {
        return new SettingEntity($name, $value);
    }
}
