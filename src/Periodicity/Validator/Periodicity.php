<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Periodicity extends Constraint
{
    public string $message = '{{ message }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
