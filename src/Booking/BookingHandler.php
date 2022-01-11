<?php

namespace Grr\GrrBundle\Booking;

use Carbon\Carbon;
use Grr\GrrBundle\Entity\Booking;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Notification\EntryEmailNotification;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class BookingHandler
{
    public function __construct(
        private RoomRepository $roomRepository,
        private NotifierInterface $notifier,
        private UserRepository $userRepository
    ) {
    }

    public function convertBookingToEntry(Booking $booking): Entry
    {
        $jour = $booking->getJour();
        $area = null;
        $room = $this->roomRepository->find($booking->getRoomId());
        if (null !== $room) {
            $area = $room->getArea();
        }

        $horaire = BookingCont::horairesTime[$booking->getHoraireId()];
        if ([] !== $horaire) {
            $startime = Carbon::instance($jour);
            $startime->hour($horaire[0]);
            $endTime = Carbon::instance($jour);
            $endTime->hour($horaire[1]);
        }

        $entry = new Entry();
        $entry->setArea($area);
        $entry->setRoom($room);
        $entry->setStartTime($startime);
        $entry->setEndTime($endTime);
        $entry->setDescription($booking->getInformations());
        $entry->setName($booking->getNom().' '.$booking->getPrenom());

        return $entry;
    }

    public function sendConfirmation(Entry $entry, string $email): void
    {
        $notification = new EntryEmailNotification('Validation de votre réservation', $entry);
        $recipient = new Recipient(
            $email
        );
        $recipientAdmin = new Recipient(
            'jf@marche.be'
        );
        $this->notifier->send($notification, $recipient, $recipientAdmin);
    }
}
