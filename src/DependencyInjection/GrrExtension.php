<?php

namespace Grr\GrrBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see https://symfony.com/doc/bundles/prepend_extension.html
 */
class GrrExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));

        $phpFileLoader->load('services.php');

        $env = $containerBuilder->getParameter('kernel.environment');

        if ('prod' !== $env) {
            $phpFileLoader->load('services_dev.php');
            $phpFileLoader->load('services_test.php');
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $bundles = $containerBuilder->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            foreach (array_keys($containerBuilder->getExtensions()) as $name) {
                switch ($name) {
                    case 'doctrine':
                        $this->loadConfig($containerBuilder, 'doctrine');
                        $this->loadConfig($containerBuilder, 'doctrine_extension');
                        break;
                    case 'twig':
                        $this->loadConfig($containerBuilder, 'twig');
                        break;
                    case 'framework':
                        $this->loadConfig($containerBuilder, 'security');
                        break;
                }
            }
        }
    }

    protected function loadConfig(ContainerBuilder $containerBuilder, string $name): void
    {
        $configs = $this->loadPhpFile($containerBuilder);
        $configs->load($name.'.php');
    }

    protected function loadPhpFile(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages/')
        );
    }
}
