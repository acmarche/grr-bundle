<?php


namespace Grr\GrrBundle\Templating\Helper;


use Grr\Core\Contrat\Front\ViewerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class RenderViewLocator
{
    /**
     * @var \Traversable
     */
    private $renders;
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    public function __construct(
        \Traversable $renders,
        ServiceLocator $serviceLocator
    ) {
        $this->renders = $renders;
        $this->serviceLocator = $serviceLocator;
    }

    public function loadAllInterface(): \Traversable
    {
        return $this->renders;
    }

    /**
     * @throws \Exception
     */
    public function loadInterfaceByKey(string $key): ViewerInterface
    {
        if ($this->serviceLocator->get($key)) {
            return $this->serviceLocator->get($key);
        }
        throw new \Exception('Aucune class trouvée pour gérer cette vue');
    }
}
