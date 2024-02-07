<?php

namespace Grr\GrrBundle\User\MessageHandler;

use Grr\Core\User\Message\UserDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

class UserDeletedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(UserDeleted $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.user.deleted');
        $this->notifier->send($notification);
    }
}
