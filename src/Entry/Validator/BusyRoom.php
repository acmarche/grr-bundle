<?php

namespace Grr\GrrBundle\Entry\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BusyRoom extends Constraint
{
    public string $message2 = 'constraint.entry.busy {{ rooms }}';

    public string $message = 'La réservation est dans une zone occupée: rooms';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
