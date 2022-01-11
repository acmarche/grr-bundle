<?php

namespace Grr\GrrBundle\Booking;

class BookingCont
{
    public const horairesTime = [
        1 => [9, 13],
        //'9h à 13h',
        [9, 17],
        //'9h à 17h',
        [13, 17],
        //'13h à 17h',
        [18, 22],
        //  '18h à 22h',
    ];

    public const horaires = [
        1 => '9h à 13h',
        '9h à 17h',
        '13h à 17h',
        '18h à 22h',
    ];
}
