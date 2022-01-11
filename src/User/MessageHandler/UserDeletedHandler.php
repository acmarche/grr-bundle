<?php

namespace Grr\GrrBundle\User\MessageHandler;

use Grr\Core\User\Message\UserDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class UserDeletedHandler implements MessageHandlerInterface
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(UserDeleted $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.user.deleted');
        $this->notifier->send($notification);
    }
}
