<?php

namespace Grr\GrrBundle\Authorization\MessageHandler;

use Grr\Core\Authorization\Message\AuthorizationUpdated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class AuthorizationUpdatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(AuthorizationUpdated $authorizationCreated): void
    {
        $notification = new FlashNotification('success', 'flash.authorization.updated');
        $this->notifier->send($notification);
    }
}
