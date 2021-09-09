<?php

namespace Grr\GrrBundle\Booking;

use Carbon\Carbon;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\GrrBundle\Entity\Booking;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Notification\EntryEmailNotification;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class BookingHandler
{
    private RoomRepository $roomRepository;
    private NotifierInterface $notifier;
    private UserRepository $userRepository;

    public function __construct(
        RoomRepository $roomRepository,
        NotifierInterface $notifier,
        UserRepository $userRepository
    ) {
        $this->roomRepository = $roomRepository;
        $this->notifier = $notifier;
        $this->userRepository = $userRepository;
    }

    public function convertBookingToEntry(Booking $booking): EntryInterface
    {
        $jour = $booking->getJour();
        $area = null;
        $room = $this->roomRepository->find($booking->getRoomId());
        if ($room) {
            $area = $room->getArea();
        }

        $horaire = BookingCont::horairesTime[$booking->getHoraireId()];
        if ($horaire) {
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

    public function sendConfirmation(Entry $entry, string $email)
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
