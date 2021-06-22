<?php

namespace Grr\GrrBundle\Area\MessageHandler;

use Grr\Core\Area\Message\AreaUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class AreaUpdatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(AreaUpdated $areaCreated): void
    {
        $notification = new FlashNotification('success', 'flash.area.updated');
        $this->notifier->send($notification);
    }
}
