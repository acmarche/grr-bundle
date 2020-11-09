<?php

namespace Grr\GrrBundle\Entry\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Vérifie que l'heure de début et de fin de l'entry respecte les heures d'ouvertures et fermetures de l'are.
 *
 * @Annotation
 */
class AreaTimeSlot extends Constraint
{
    /**
     * end time exceeds opening time.
     *
     * @var string
     * */
    public $message_exceeds = 'constraint.entry.area.exceeds';
    /**
     * Start time must be greater than room opening time.
     *
     * @var string
     */
    public $message_greater = 'constraint.entry.area.greater';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
