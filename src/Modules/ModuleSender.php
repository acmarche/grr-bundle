<?php

namespace Grr\GrrBundle\Modules;

use Grr\Core\Modules\GrrModuleSenderInterface;
use Grr\Core\Modules\GrrModuleInterface;

class ModuleSender implements GrrModuleSenderInterface
{
    /**
     * @var GrrModuleInterface[]
     */
    public $modules = [];

    public function addModule(GrrModuleInterface $module): void
    {
        $this->modules[] = $module;
    }

    public function postContent(): void
    {
        foreach ($this->modules as $module) {
            $module->doSomething();
        }
    }
}
