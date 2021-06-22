<?php

namespace Grr\GrrBundle\Modules;

use Grr\Core\Contrat\Modules\GrrModuleInterface;
use Grr\Core\Contrat\Modules\GrrModuleSenderInterface;

class ModuleSender implements GrrModuleSenderInterface
{
    /**
     * @var GrrModuleInterface[]
     */
    public array $modules = [];

    public function construct(iterable $modules): void
    {
        /*
         * pour dans services.php
         * $services->set(ModuleSender::class)
         * ->arg('$modules', tagged_iterator('grr.module'));
         */
    }

    public function addModule(GrrModuleInterface $grrModule): void
    {
        $this->modules[] = $grrModule;
    }

    public function __invoke(): void
    {
        dump($this->modules);
        foreach ($this->modules as $module) {
            $module->doSomething();
        }
    }

    public function postContent()
    {
        dump($this->modules);
    }
}
