<?php
/**
 * This file is part of sf5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 16/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle;

use Grr\GrrBundle\DependencyInjection\Compiler\ModulesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class GrrBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
        $env = $builder->getParameter('kernel.environment');
        if ('dev' == $env) {
            $container->import('../config/services_dev.php');
        }

        if ('test' == $env) {
            $container->import('../config/services_test.php');
        }
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/packages/twig.php');
        $container->import('../config/packages/security.php');
        $container->import('../config/packages/doctrine.php');
        $container->import('../config/packages/doctrine_extension.php');
    }

    public function build(ContainerBuilder $containerBuilder): void
    {
        parent::build($containerBuilder);

        $containerBuilder->addCompilerPass(new ModulesPass());
    }
}
