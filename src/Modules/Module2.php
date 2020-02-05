<?php

namespace Grr\GrrBundle\Modules;

use Grr\Core\Contrat\Modules\GrrModuleInterface;

class Module2 implements GrrModuleInterface
{
    public function getName(): string
    {
        return 'module2';
    }

    public function getVersion(): string
    {
        return '1.0';
    }

    public function doSomething(): void
    {
        echo 'Module 1';
    }
}
