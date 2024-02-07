<?php

namespace Grr\GrrBundle\Password\MessageHandler;

use Grr\Core\Password\Message\PasswordUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class PasswordUpdatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(PasswordUpdated $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.password.updated');
        $this->notifier->send($notification);
    }
}
