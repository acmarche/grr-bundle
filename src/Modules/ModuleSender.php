<?php

namespace Grr\GrrBundle\Modules;

use Grr\Core\Contrat\Modules\GrrModuleInterface;
use Grr\Core\Contrat\Modules\GrrModuleSenderInterface;

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
        dump($this->modules);
        foreach ($this->modules as $module) {
            $module->doSomething();
        }
    }
}
