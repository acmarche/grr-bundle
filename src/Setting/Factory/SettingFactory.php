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
    /**
     * @param string|array $value
     */
    public function createNew(string $name, $value): SettingEntity
    {
        return new SettingEntity($name, $value);
    }
}
