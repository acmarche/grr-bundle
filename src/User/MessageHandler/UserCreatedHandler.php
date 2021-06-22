<?php

namespace Grr\GrrBundle\User\MessageHandler;

use Grr\Core\User\Message\UserCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class UserCreatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(UserCreated $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.user.created');
        $this->notifier->send($notification);
    }
}
