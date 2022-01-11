<?php

namespace Grr\GrrBundle\DependencyInjection\Compiler;

use Grr\Core\Contrat\Modules\GrrModuleSenderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

//https://symfony.com/doc/current/service_container/tags.html
class ModulesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $containerBuilder): void
    {
        // always first check if the primary service is defined
        if (! $containerBuilder->has(GrrModuleSenderInterface::class)) {
            return;
        }

        $definition = $containerBuilder->findDefinition(GrrModuleSenderInterface::class);

        // find all service IDs with the grr_module tag
        $taggedServices = $containerBuilder->findTaggedServiceIds('grr.module');

        foreach (array_keys($taggedServices) as $id) {
            // add the transport service to the TransportChain service
            $definition->addMethodCall('addModule', [new Reference($id)]);
        }
    }
}
