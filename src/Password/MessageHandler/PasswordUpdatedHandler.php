<?php

namespace Grr\GrrBundle\Password\MessageHandler;

use Grr\Core\Password\Message\PasswordUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class PasswordUpdatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function __invoke(PasswordUpdated $userCreated): void
    {
        $notification = new FlashNotification('success', 'flash.password.updated');
        $this->notifier->send($notification);
    }
}
