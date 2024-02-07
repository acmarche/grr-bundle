<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryCreatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(TypeEntryCreated $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.created');
        $this->notifier->send($notification);
    }
}
