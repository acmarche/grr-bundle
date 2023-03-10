<?php

namespace Grr\GrrBundle\Room\MessageHandler;

use Grr\Core\Room\Message\RoomDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class RoomDeletedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(RoomDeleted $roomCreated): void
    {
        $notification = new FlashNotification('success', 'flash.room.deleted');
        $this->notifier->send($notification);
    }
}
