<?php

namespace Grr\GrrBundle\Authorization\MessageHandler;

use Grr\Core\Authorization\Message\AuthorizationDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class AuthorizationDeletedHandler implements MessageHandlerInterface
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(AuthorizationDeleted $authorizationCreated): void
    {
        $notification = new FlashNotification('success', 'flash.authorization.deleted');
        $this->notifier->send($notification);
    }
}
