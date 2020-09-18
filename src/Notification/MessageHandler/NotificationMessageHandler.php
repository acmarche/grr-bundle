<?php

namespace Grr\GrrBundle\Notification\MessageHandler;

use Grr\GrrBundle\Notification\Message\NotificationUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationMessageHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(NotificationUpdated $notificationUpdated): void
    {
        $this->flashBag->add('success', 'flash.notification.updated');
    }
}
