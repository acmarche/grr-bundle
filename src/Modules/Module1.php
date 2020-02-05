<?php

namespace Grr\GrrBundle\Modules;

use Grr\Core\Contrat\Modules\GrrModuleInterface;

class Module1 implements GrrModuleInterface
{
    public function getName(): string
    {
        return 'module1';
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
