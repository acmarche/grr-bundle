<?php

namespace Grr\GrrBundle\Preference\MessageHandler;

use Grr\Core\Preference\Message\PreferenceUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class PreferenceUpdatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(PreferenceUpdated $preferenceCreated): void
    {
        $notification = new FlashNotification('success', 'flash.notification.updated');
        $this->notifier->send($notification);
    }
}
