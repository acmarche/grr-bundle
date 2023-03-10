<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class PeriodicityDeletedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(PeriodicityDeleted $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.periodicity.deleted');
        $this->notifier->send($notification);
    }
}
