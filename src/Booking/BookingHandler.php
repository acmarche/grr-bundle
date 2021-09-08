<?php

namespace Grr\GrrBundle\Booking;

use Carbon\Carbon;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\GrrBundle\Entity\Booking;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Room\Repository\RoomRepository;

class BookingHandler
{
    private RoomRepository $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
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
}
