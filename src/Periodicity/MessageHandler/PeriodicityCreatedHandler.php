<?php

namespace Grr\GrrBundle\Periodicity\MessageHandler;

use Grr\Core\Periodicity\Message\PeriodicityCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class PeriodicityCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(PeriodicityCreated $periodicityCreated): void
    {
        $notification = new FlashNotification('success', 'flash.periodicity.created');
        $this->notifier->send($notification);
    }
}
