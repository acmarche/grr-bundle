<?php

namespace Grr\GrrBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see https://symfony.com/doc/bundles/prepend_extension.html
 */
class GrrExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $container)
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfigDoctrine($container);
                        break;
                }
            }
        }
    }

    protected function loadConfigDoctrine(ContainerBuilder $container)
    {
        $configs = $this->loadYamlFile($container, '/packages/doctrine.yaml');
        $container->prependExtensionConfig('doctrine', $configs);
    }

    protected function loadYamlFile(ContainerBuilder $container, $name): array
    {
        $configs = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config'.$name)
        );

        return [];
    }
}
