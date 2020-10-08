<?php

namespace Grr\GrrBundle\TypeEntry\MessageHandler;

use Grr\Core\TypeEntry\Message\TypeEntryUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class TypeEntryUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(TypeEntryUpdated $typeEntryCreated): void
    {
        $notification = new FlashNotification('success', 'flash.typeEntry.updated');
        $this->notifier->send($notification);
    }
}
