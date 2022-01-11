<?php

namespace Grr\GrrBundle\Notification;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class FlashNotification extends Notification
{
    public function __construct(
        private string $type,
        string $message
    ) {
        parent::__construct($message);
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['browsergrr'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
