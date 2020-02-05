<?php

namespace Grr\GrrBundle\DependencyInjection\Compiler;

use Grr\Core\Contrat\Modules\GrrModuleSenderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModulesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(GrrModuleSenderInterface::class)) {
            return;
        }

        $definition = $container->findDefinition(GrrModuleSenderInterface::class);

        // find all service IDs with the grr_module tag
        $taggedServices = $container->findTaggedServiceIds('grr_module');

        foreach ($taggedServices as $id => $module) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addModule', [new Reference($id)]);
        }
    }
}
