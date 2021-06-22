<?php

namespace Grr\GrrBundle\Area\MessageHandler;

use Grr\Core\Area\Message\AreaDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class AreaDeletedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(AreaDeleted $areaCreated): void
    {
        $notification = new FlashNotification('success', 'flash.area.deleted');
        $this->notifier->send($notification);
    }
}
