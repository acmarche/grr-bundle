<?php

namespace Grr\GrrBundle\User\MessageHandler;

use Grr\Core\User\Message\UserUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class UserUpdatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(UserUpdated $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.user.updated');
        $this->notifier->send($notification);
    }
}
