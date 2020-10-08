<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\Core\Entry\Message\EntryDeleted;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Notification\EntryEmailNotification;
use Grr\GrrBundle\Notification\FlashNotification;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class EntryDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;
    /**
     * @var EntryRepository
     */
    private $entryRepository;
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EmailPreferenceRepository
     */
    private $emailPreferenceRepository;

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

    public function __invoke(EntryDeleted $entryCreated): void
    {
        $this->sendNotificationToBrowser();
        $this->sendNotificationByEmailForReservedBy($entryCreated);
        $this->sendNotificationByEmail($entryCreated);
    }

    private function sendNotificationToBrowser()
    {
        $notification = new FlashNotification('success', 'flash.entry.deleted');
        $this->notifier->send($notification);
    }

    private function sendNotificationByEmail(EntryDeleted $entryCreated)
    {
        $entry = $this->entryRepository->find($entryCreated->getEntryId());
        $notification = new EntryEmailNotification('La réservation a été supprimée: ', $entry);

        $room = $entry->getRoom();
        $area = $room->getArea();

        $authorizations = $this->authorizationHelper->findByAreaOrRoom($area, $room);
        $users = array_map(
            function ($authorization) {
                return $authorization->getUser();
            },
            $authorizations
        );

        $administrators = $this->userRepository->getGrrAdministrators();
        $users = array_merge($users, $administrators);
        $recipients = [];

        foreach ($users as $user) {
            $preference = $this->emailPreferenceRepository->findOneByUser($user);
            if ($preference && true === $preference->getOnCreated()) {
                $recipients[] =
                    new Recipient(
                        $user->getEmail()
                    );
            }
        }

        if (count($recipients) > 0) {
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
            if ($user) {
                $recipient = new Recipient(
                    $user->getEmail()
                );

                $this->notifier->send($notification, $recipient);
            }
        }
    }
}
