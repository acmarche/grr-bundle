<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\GrrBundle\Controller\Front\FrontControllerInterface;
use Grr\GrrBundle\Navigation\RessourceSelectedHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class RessourceSelectedSubscriber implements EventSubscriberInterface
{
    /**
     * @var RessourceSelectedHelper
     */
    private $ressourceSelectedHelper;

    public function __construct(RessourceSelectedHelper $ressourceSelectedHelper)
    {
        $this->ressourceSelectedHelper = $ressourceSelectedHelper;
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $controller = $event->getController();

        /**
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format.
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof FrontControllerInterface) {
            $area = $event->getRequest()->get('area');
            $room = $event->getRequest()->get('room');
            /*
             * if not set in url, force by user all ressources
             */
            if (!$room) {
                $room = -1;
            }

            if ($area instanceof AreaInterface) {
                $this->ressourceSelectedHelper->setSelected($area->getId(), $room);
            }
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
