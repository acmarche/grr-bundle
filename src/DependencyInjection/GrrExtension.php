<?php

namespace Grr\GrrBundle\DependencyInjection;

use Doctrine\Common\EventSubscriber;
use Grr\Core\Contrat\Modules\GrrModuleInterface;
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
class GrrExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/../../config'));

        // @see https://github.com/doctrine/DoctrineBundle/issues/674
        /*   $container->registerForAutoconfiguration(EventSubscriber::class)
               ->addTag(self::DOCTRINE_EVENT_SUBSCRIBER_TAG);
*/
        $phpFileLoader->load('services.php');
        $phpFileLoader->load('services_dev.php');
        $phpFileLoader->load('services_test.php');

        //auto tag GrrModuleInterface
        $containerBuilder->registerForAutoconfiguration(GrrModuleInterface::class)
            ->addTag('grr.module');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     */
    public function prepend(ContainerBuilder $containerBuilder): void
    {
        // get all bundles
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
        $configs = $this->loadYamlFile($containerBuilder);

        $configs->load($name.'.php');
        //  $container->prependExtensionConfig('doctrine', $configs);
    }

    protected function loadYamlFile(ContainerBuilder $containerBuilder): PhpFileLoader
    {
        return new PhpFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__.'/../../config/packages/')
        );
    }
}
