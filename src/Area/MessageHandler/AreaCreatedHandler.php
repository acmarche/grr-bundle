<?php

namespace Grr\GrrBundle\Area\MessageHandler;

use Grr\Core\Area\Message\AreaCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class AreaCreatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(AreaCreated $areaCreated): void
    {
        $notification = new FlashNotification('success', 'flash.area.created');
        $this->notifier->send($notification);
    }
}
