<?php

namespace Grr\GrrBundle\Validator\Entry;

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
    public $message = 'entry.constraint.duration.time.float';
}
