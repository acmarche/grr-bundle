<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\Core\Entry\Message\EntryCreated;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Notification\EntryEmailNotification;
use Grr\GrrBundle\Notification\FlashNotification;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class EntryCreatedHandler implements MessageHandlerInterface
{
    private NotifierInterface $notifier;
    private EntryRepository $entryRepository;
    private AuthorizationHelper $authorizationHelper;
    private UserRepository $userRepository;
    private EmailPreferenceRepository $emailPreferenceRepository;

    public function __construct(
        NotifierInterface $notifier,
        EntryRepository $entryRepository,
        UserRepository $userRepository,
        AuthorizationHelper $authorizationHelper,
        EmailPreferenceRepository $emailPreferenceRepository
    ) {
        $this->notifier = $notifier;
        $this->entryRepository = $entryRepository;
        $this->authorizationHelper = $authorizationHelper;
        $this->userRepository = $userRepository;
        $this->emailPreferenceRepository = $emailPreferenceRepository;
    }

    public function __invoke(EntryCreated $entryCreated): void
    {
        $this->sendNotificationToBrowser();
        $this->sendNotificationByEmailForReservedBy($entryCreated);
        $this->sendNotificationByEmail($entryCreated);
    }

    private function sendNotificationToBrowser()
    {
        $notification = new FlashNotification('success', 'flash.entry.created');
        $this->notifier->send($notification);
    }

    private function sendNotificationByEmail(EntryCreated $entryCreated)
    {
        $entry = $this->entryRepository->find($entryCreated->getEntryId());
        $notification = new EntryEmailNotification('Nouvelle réservation: ', $entry);

        $room = $entry->getRoom();
        $area = $room->getArea();

        $authorizations = $this->authorizationHelper->findByAreaOrRoom($area, $room);
        $users = array_map(
            fn ($authorization) => $authorization->getUser(),
            $authorizations
        );

        $administrators = $this->userRepository->getGrrAdministrators();
        $users = array_merge($users, $administrators);
        $emails = [];

        foreach ($users as $user) {
            $preference = $this->emailPreferenceRepository->findOneByUser($user);
            if ($preference && true === $preference->getOnCreated() && !in_array($user->getEmail(), $emails)) {
                $emails[] = $user->getEmail();
            }
        }

        $recipients = [];
        foreach ($emails as $email) {
            $recipients[] = new Recipient($email);
        }

        if (count($recipients) > 0) {
            $this->notifier->send($notification, ...$recipients);
        }
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation au nom d'un autre utilisateur,
     * ce dernier en est averti automatiquement par un message e-mail.
     */
    private function sendNotificationByEmailForReservedBy(EntryCreated $entryCreated): void
    {
        $entry = $this->entryRepository->find($entryCreated->getEntryId());
        if (null !== $entry->getReservedFor() && $reservedFor = $entry->getReservedFor() !== $entry->getCreatedBy()) {
            $notification = new EntryEmailNotification('Une réservation a été faire pour vous : ', $entry);
            $user = $this->userRepository->loadByUserNameOrEmail($reservedFor);
            if ($user !== null) {
                $recipient = new Recipient(
                    $user->getEmail()
                );

                $this->notifier->send($notification, $recipient);
            }
        }
    }
}
