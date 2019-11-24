<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 1/03/19
 * Time: 20:08.
 */

namespace Grr\GrrBundle\Manager;

interface ManagerInterface
{
    public function persist();

    public function remove();

    public function flush();

    public function insert();
}
