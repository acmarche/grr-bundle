<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class PeriodicityDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(PeriodicityDeleted $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.periodicity.deleted');
        $this->notifier->send($notification);
    }
}
