<?php

namespace Grr\GrrBundle\Area\MessageHandler;

use Grr\Core\Area\Message\AreaDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class AreaDeletedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(AreaDeleted $areaCreated): void
    {
        $notification = new FlashNotification('success', 'flash.area.deleted');
        $this->notifier->send($notification);
    }
}
