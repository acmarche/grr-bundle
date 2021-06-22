<?php

namespace Grr\GrrBundle\Entry\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BusyRoom extends Constraint
{
    public string $message = 'constraint.entry.busy';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
