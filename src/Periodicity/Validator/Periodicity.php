<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Periodicity extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    /**
     * @var string
     */
    public $message = '{{ message }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
