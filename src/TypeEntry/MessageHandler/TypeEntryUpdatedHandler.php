<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryUpdatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(TypeEntryUpdated $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.updated');
        $this->notifier->send($notification);
    }
}
