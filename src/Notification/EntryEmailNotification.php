<?php

namespace Grr\GrrBundle\Notification;

use Grr\Core\Contrat\Entity\EntryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

/**
 * Class EntryEmailNotification.
 */
class EntryEmailNotification extends Notification implements EmailNotificationInterface
{
    private EntryInterface $entry;
    private ?string $actionUrl;
    private ?string $actionText;

    public function __construct(
        string $sujet,
        EntryInterface $entry,
        ?string $actionUrl = null,
        ?string $actionText = 'Consulter'
    ) {
        $this->entry = $entry;
        parent::__construct($sujet.$entry->getName());
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    /**
     * Permet de personnaliser l'email.
     */
    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        // ici je ne sais pas retirer le LOW dans le sujet
        // $message = EmailMessage::fromNotification($this, $recipient);
        $message = NotificationEmail::asPublicEmail();
        $message
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->content($this->getContent() ?: $this->getSubject())
            ->htmlTemplate('@Grr/emails/notification/entry_created_notification.html.twig')
            ->context(
                [
                    'entry' => $this->entry,
                    'importance' => self::IMPORTANCE_MEDIUM,
                    'action_url' => $this->actionUrl,
                    'action_text' => $this->actionText,
                ]
            );

        return new EmailMessage($message);
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_LOW);

        return ['email'];
    }
}
