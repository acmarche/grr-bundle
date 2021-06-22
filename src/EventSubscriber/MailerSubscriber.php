<?php

namespace Grr\GrrBundle\EventSubscriber;

use Grr\GrrBundle\Mailer\EmailFactory;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Émettre un courrier quand je modifie mon agenda
 * Émettre un courrier quand quelqu'un d'autre modifie mon agenda
 * Quand je modifie mon agenda, émettre un courrier à.
 */
/**
 * Auto : Par ailleurs, lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation,
 * certains utilisateurs peuvent être pravenus par e-mail.
 * Pour chaque ressource, vous pouvez désigner un ou plusieurs utilisateurs à prévenir :.
 */

/**
 * Class MailerSubscriber.
 */
class MailerSubscriber
{
    public function sendMail(BaseEntryEvent $baseEntryEvent): void
    {
        $this->currentUser = $this->security->getUser();
        $this->entry = $baseEntryEvent->getEntry();
        $this->username = $this->entry->getReservedFor();
        $this->autosendChangeByOtherUser();
        $this->AutosendChangeBySelf();
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation au nom d'un autre utilisateur,
     * ce dernier en est averti automatiquement par un message e-mail.
     */
    protected function autosendChangeByOtherUser(): void
    {
        if ($this->username = $this->entry->getReservedFor() === $this->currentUser->getUsername()) {
            return;
        }

        $this->sendEmail();
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource,
     * modifie ou bien supprime une réservation pour lui-même (dont il est bénéficiaire),
     * un email de confirmation soit systématiquement envoyé.
     */
    protected function AutosendChangeBySelf(): void
    {
        if (true === (bool) $this->settingRepository->getValueByName('send_always_mail_to_creator')) {
            $this->sendEmail();
        }
    }

    protected function sendEmail(): void
    {
        $subject = $this->translator->trans('mail.change.subject');
        $to = $this->userRepository->findOneBy(['username' => $this->username]);
        if (!$to) {
            return;
        }

        $message = EmailFactory::createNewTemplated();
        $message
            ->to($to->getEmail())
            ->from($this->currentUser->getEmail())
            ->subject($subject)
            ->textTemplate('@Grr/email/entry_change.txt.twig')
            ->context(
                [
                    'entry' => $this->entry,
                    'user' => $this->currentUser,
                ]
            );

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            $this->flashBag->add('error', 'flash.mail.error ' . $e->getMessage());
        }
    }

    protected function niceEmail(): void
    {
    }
}
