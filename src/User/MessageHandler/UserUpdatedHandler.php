<?php

namespace Grr\GrrBundle\User\MessageHandler;

use Grr\Core\User\Message\UserUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class UserUpdatedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(UserUpdated $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.user.updated');
        $this->notifier->send($notification);
    }
}
