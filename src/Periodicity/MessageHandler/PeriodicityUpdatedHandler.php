<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityUpdated;
use Grr\GrrBundle\Notification\FlashNotification;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class PeriodicityUpdatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(PeriodicityUpdated $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.entry.updated');
        $this->notifier->send($notification);
    }
}
