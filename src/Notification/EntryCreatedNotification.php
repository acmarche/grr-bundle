<?php

namespace Grr\GrrBundle\Notification;

use Grr\GrrBundle\Entity\Entry;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class EntryCreatedNotification extends Notification implements EmailNotificationInterface
{
    private $entry;
    private $sujet;

    public function __construct( string $sujet,Entry $entry)
    {
        $this->entry = $entry;
        $this->sujet = $sujet;

        parent::__construct($sujet.$entry->getName());
    }

    /**
     * Permet de personnaliser l'email.
     */
    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);
        $message->getMessage()
            ->htmlTemplate('@Grr/emails/notification/entry_created_notification.html.twig')
            ->context(['entry' => $this->entry]);

        return $message;
    }

    public function getChannels(Recipient $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_MEDIUM);

        return ['email'];
    }
}
