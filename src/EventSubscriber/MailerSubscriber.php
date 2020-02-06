<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\Core\Entry\Events\EntryEventCreated;
use Grr\GrrBundle\Mailer\EmailFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Auto : Par ailleurs, lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation,
 * certains utilisateurs peuvent être pravenus par e-mail.
 * Pour chaque ressource, vous pouvez désigner un ou plusieurs utilisateurs à prévenir :.
 */

/**
 * Class MailerSubscriber.
 */
class MailerSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            EntryEventCreated::class
        ];
    }

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation au nom d'un autre utilisateur,
     * ce dernier en est averti automatiquement par un message e-mail.
     */
    public function AutosendChangeByOtherUser(): void
    {
        $message = EmailFactory::createNewTemplated();
        $message
            ->to('jf@marche.be')
            ->from('jf@marche.be')
            ->subject('test')
            ->htmlTemplate('email/welcome.html.twig')
            ->context(
                [
                    'zeze' => 'lolo',
                ]
            );
        $this->mailer->send($message);
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource,
     * modifie ou bien supprime une réservation pour lui-même (dont il est bénéficiaire),
     * un email de confirmation soit systématiquement envoyé.
     */
    public function AutosendChangeBySelf(string $email): void
    {
        $message = EmailFactory::createNewTemplated();
        $message
            ->to($email)
            ->from('jf@marche.be')
            ->subject('test')
            ->htmlTemplate('email/welcome.html.twig')
            ->context(
                [
                    'zeze' => 'lolo',
                ]
            );
        $this->send($message);
    }

}
