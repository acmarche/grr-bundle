<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\GrrBundle\Entry\Message\EntryCreated;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Notification\EntryCreatedNotification;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class EntryCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var NotifierInterface
     */
    private $notifier;
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(NotifierInterface $notifier, EntryRepository $entryRepository)
    {
        $this->notifier = $notifier;
        $this->entryRepository = $entryRepository;
    }

    public function __invoke(EntryCreated $entryCreated): void
    {
        $this->sendNotificationToBrowser();
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
        $notification = new EntryCreatedNotification($entry, 'str');

        $recipients = [new Recipient(
            'jf@marche.be',
        )];

        $this->notifier->send($notification, ...$recipients);
    }
}
