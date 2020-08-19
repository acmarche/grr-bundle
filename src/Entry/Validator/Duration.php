<?php

namespace Grr\GrrBundle\Entry\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Duration extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    /**
     * @var string
     */
    public $message = 'constraint.entry.duration.time_float';
}
