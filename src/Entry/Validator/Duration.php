<?php

namespace Grr\GrrBundle\Entry\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Duration extends Constraint
{
    public string $message = 'constraint.entry.duration.time_float';
}
