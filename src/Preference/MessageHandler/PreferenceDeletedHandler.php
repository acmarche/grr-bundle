<?php

namespace Grr\GrrBundle\Preference\MessageHandler;

use Grr\Core\Preference\Message\PreferenceDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class PreferenceDeletedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(PreferenceDeleted $preferenceCreated): void
    {
        $notification = new FlashNotification('success', 'flash.preference.deleted');
        $this->notifier->send($notification);
    }
}
