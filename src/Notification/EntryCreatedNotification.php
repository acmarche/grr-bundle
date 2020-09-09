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
    private $reviewUrl;

    public function __construct(Entry $entry, string $reviewUrl)
    {
        $this->entry = $entry;
        $this->reviewUrl = $reviewUrl;

        parent::__construct($entry->getName().' a été ajouté');
    }

    /**
     * Permet de personnaliser l'email.
     */
    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);
        $message->getMessage()
            ->htmlTemplate('@Grr/emails/comment_notification.html.twig')
            ->context(['entry' => $this->entry]);

        return $message;
    }

    public function getChannels(Recipient $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_MEDIUM);

        return ['email'];
    }
}
