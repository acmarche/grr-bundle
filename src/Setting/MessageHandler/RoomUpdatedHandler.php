<?php

namespace Grr\GrrBundle\Setting\MessageHandler;

use Grr\Core\Setting\Message\SettingUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class RoomUpdatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(SettingUpdated $settingUpdated): void
    {
        $notification = new FlashNotification('success', 'flash.setting.updated');
        $this->notifier->send($notification);
    }
}
