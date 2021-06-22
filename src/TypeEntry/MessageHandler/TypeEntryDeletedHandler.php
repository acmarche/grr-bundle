<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryDeletedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(TypeEntryDeleted $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.deleted');
        $this->notifier->send($notification);
    }
}
