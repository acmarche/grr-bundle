<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PeriodicityEveryDay extends Constraint
{
    public string $message = 'The value "{{ value }}" is not valid.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
