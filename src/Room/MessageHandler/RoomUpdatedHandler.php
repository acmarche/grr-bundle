<?php

namespace Grr\GrrBundle\Room\MessageHandler;

use Grr\Core\Room\Message\RoomUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class RoomUpdatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(RoomUpdated $roomCreated): void
    {
        $notification = new FlashNotification('success', 'flash.room.updated');
        $this->notifier->send($notification);
    }
}
