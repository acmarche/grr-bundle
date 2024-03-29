<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\Core\Contrat\Entity\RoomInterface;
use Grr\GrrBundle\Controller\Front\FrontControllerInterface;
use Grr\GrrBundle\Navigation\RessourceSelectedHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class RessourceSelectedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RessourceSelectedHelper $ressourceSelectedHelper,
    ) {
    }

    public function onControllerEvent(ControllerEvent $controllerEvent): void
    {
        $controller = $controllerEvent->getController();

        /**
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format.
         */
        if (!\is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof FrontControllerInterface) {
            $area = $controllerEvent->getRequest()->get('area');
            $room = $controllerEvent->getRequest()->get('room');
            /**
             * if not set in url, force by user all ressources
             */
            if (!$room) {
                $room = -1;
            }

            if ($room instanceof RoomInterface) {
                $room = $room->getId();
            }

            if ($area instanceof AreaInterface) {
                $area = $area->getId();
            }

            $this->ressourceSelectedHelper->setSelected($area, $room);
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
