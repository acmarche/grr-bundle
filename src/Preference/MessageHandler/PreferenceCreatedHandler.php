<?php

namespace Grr\GrrBundle\Preference\MessageHandler;

use Grr\Core\Preference\Message\PreferenceCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class PreferenceCreatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(PreferenceCreated $preferenceCreated): void
    {
        $notification = new FlashNotification('success', 'flash.preference.created');
        $this->notifier->send($notification);
    }
}
