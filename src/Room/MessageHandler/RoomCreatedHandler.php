<?php

namespace Grr\GrrBundle\Room\MessageHandler;

use Grr\Core\Room\Message\RoomCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class RoomCreatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(RoomCreated $roomCreated): void
    {
        $notification = new FlashNotification('success', 'flash.room.created');
        $this->notifier->send($notification);
    }
}
