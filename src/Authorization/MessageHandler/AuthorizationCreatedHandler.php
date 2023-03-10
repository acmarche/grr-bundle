<?php

namespace Grr\GrrBundle\Authorization\MessageHandler;

use Grr\Core\Authorization\Message\AuthorizationCreated;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;

#[AsMessageHandler]
class AuthorizationCreatedHandler
{
    public function __construct(
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(AuthorizationCreated $authorizationCreated): void
    {
        $notification = new FlashNotification('success', 'flash.authorization.created');
        $this->notifier->send($notification);
    }
}
