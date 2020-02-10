<?php

namespace Grr\GrrBundle\Validator\Entry;

use Symfony\Component\Validator\Constraint;

/**
 * Vérifie que l'heure de début et de fin de l'entry respecte les heures d'ouvertures et fermetures de l'are.
 *
 * @Annotation
 */
class AreaTimeSlot extends Constraint
{
    /**
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     *
     * @var string
     */

    //end time exceeds opening time
    public $message_exceeds = 'constraint.entry.area.exceeds';
    //Start time must be greater than room opening time
    public $message_greater = 'constraint.entry.area.greater';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
