<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class PeriodicityCreatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(PeriodicityCreated $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.periodicity.created');
        $this->notifier->send($notification);
    }
}
