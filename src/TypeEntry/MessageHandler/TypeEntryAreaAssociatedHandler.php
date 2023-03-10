<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryAreaAssociated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryAreaAssociatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(TypeEntryAreaAssociated $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.area.setTypeEntry');
        $this->notifier->send($notification);
    }
}
