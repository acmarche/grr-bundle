<?php

namespace Grr\GrrBundle\Validator\Entry;

use Grr\Core\Contrat\Entity\EntryInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AreaTimeSlotValidator extends ConstraintValidator
{
    /**
     * @param EntryInterface $entry
     * @param AreaTimeSlot   $constraint
     */
    public function validate($entry, Constraint $constraint): void
    {
        if (!$entry instanceof EntryInterface) {
            throw new InvalidArgumentException($entry, 0);
        }

        $area = $entry->getArea();

        if ($entry->getStartTime()->format('G') < $area->getStartTime()) {
            $this->context->buildViolation($constraint->message_greater)
                ->addViolation();
        }

        if ($entry->getEndTime()->format('G') > $area->getEndTime()) {
            $this->context->buildViolation($constraint->message_exceeds)
                ->addViolation();
        }
    }
}
