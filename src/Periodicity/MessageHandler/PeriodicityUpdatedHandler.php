<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class PeriodicityUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(PeriodicityUpdated $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.entry.updated');
        $this->notifier->send($notification);
    }
}
