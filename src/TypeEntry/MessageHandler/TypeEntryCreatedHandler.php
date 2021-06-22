<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryCreatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(TypeEntryCreated $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.created');
        $this->notifier->send($notification);
    }
}
