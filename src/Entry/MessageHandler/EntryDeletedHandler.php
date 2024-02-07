<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\GrrBundle\Entity\Security\User;
use Grr\Core\Entry\Message\EntryDeleted;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Notification\EntryEmailNotification;
use Grr\GrrBundle\Notification\FlashNotification;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsMessageHandler]
class EntryDeletedHandler
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly EntryRepository $entryRepository,
        private readonly UserRepository $userRepository,
        private readonly AuthorizationHelper $authorizationHelper,
        private readonly EmailPreferenceRepository $emailPreferenceRepository
    ) {
    }

    public function __invoke(EntryDeleted $entryCreated): void
    {
        $this->sendNotificationToBrowser();
        $this->sendNotificationByEmailForReservedBy($entryCreated);
        $this->sendNotificationByEmail($entryCreated);
    }

    private function sendNotificationToBrowser(): void
    {
        $notification = new FlashNotification('success', 'flash.entry.deleted');
        $this->notifier->send($notification);
    }

    private function sendNotificationByEmail(EntryDeleted $entryCreated): void
    {
        $entry = $this->entryRepository->find($entryCreated->getEntryId());
        $notification = new EntryEmailNotification('La réservation a été supprimée: ', $entry);

        $room = $entry->getRoom();
        $area = $room->getArea();

        $authorizations = $this->authorizationHelper->findByAreaOrRoom($area, $room);
        $users = array_map(
            static fn($authorization) => $authorization->getUser(),
            $authorizations
        );

        $administrators = $this->userRepository->getGrrAdministrators();
        $users = array_merge($users, $administrators);
        $emails = [];

        foreach ($users as $user) {
            $preference = $this->emailPreferenceRepository->findOneByUser($user);
            if ($preference && $preference->getOnDeleted() && ! \in_array($user->getEmail(), $emails)) {
                $emails[] = $user->getEmail();
            }
        }

        $recipients = [];
        foreach ($emails as $email) {
            $recipients[] = new Recipient($email);
        }

        if ([] !== $recipients) {
            $this->notifier->send($notification, ...$recipients);
        }
    }

    /**
     * Lorsqu'un utilisateur réserve une ressource, modifie ou bien supprime une réservation au nom d'un autre utilisateur,
     * ce dernier en est averti automatiquement par un message e-mail.
     */
    private function sendNotificationByEmailForReservedBy(EntryDeleted $entryCreated): void
    {
        $entry = $this->entryRepository->find($entryCreated->getEntryId());
        if (null !== $entry->getReservedFor() && $reservedFor = $entry->getReservedFor() !== $entry->getCreatedBy()) {
            $notification = new EntryEmailNotification('Une réservation a été supprimée pour vous : ', $entry);
            $user = $this->userRepository->loadByUserNameOrEmail($reservedFor);
            if ($user instanceof User) {
                $recipient = new Recipient(
                    $user->getEmail()
                );

                $this->notifier->send($notification, $recipient);
            }
        }
    }
}
