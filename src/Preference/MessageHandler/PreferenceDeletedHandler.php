<?php

namespace Grr\GrrBundle\Preference\MessageHandler;

use Grr\Core\Preference\Message\PreferenceDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class PreferenceDeletedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(PreferenceDeleted $preferenceCreated): void
    {
        $notification = new FlashNotification('success', 'flash.preference.deleted');
        $this->notifier->send($notification);
    }
}
