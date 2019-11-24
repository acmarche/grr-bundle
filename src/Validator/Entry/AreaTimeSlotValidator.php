<?php

namespace Grr\GrrBundle\Validator\Entry;

use InvalidArgumentException;
use Grr\GrrBundle\Entity\Entry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AreaTimeSlotValidator extends ConstraintValidator
{
    /**
     * @param Entry        $entry
     * @param AreaTimeSlot $constraint
     */
    public function validate($entry, Constraint $constraint): void
    {
        if (!$entry instanceof Entry) {
            throw new InvalidArgumentException($entry, 0);
        }

        $area = $entry->getArea();

        if ($entry->getStartTime()->format('G') < $area->getStartTime()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{param1}}', 'début')
                ->setParameter('{{param2}}', 'grande')
                ->setParameter('{{param3}}', 'd\'ouverture')
                ->addViolation();
        }

        if ($entry->getEndTime()->format('G') > $area->getEndTime()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{param1}}', 'fin')
                ->setParameter('{{param2}}', 'petite')
                ->setParameter('{{param3}}', 'de fermeture')
                ->addViolation();
        }
    }
}
