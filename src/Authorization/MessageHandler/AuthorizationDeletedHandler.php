<?php

namespace Grr\GrrBundle\Authorization\MessageHandler;

use Grr\Core\Authorization\Message\AuthorizationDeleted;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class AuthorizationDeletedHandler
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
