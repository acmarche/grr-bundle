<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryDeletedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(TypeEntryDeleted $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.deleted');
        $this->notifier->send($notification);
    }
}
