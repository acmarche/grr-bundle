<?php

namespace Grr\GrrBundle\Validator\Periodicity;

use Carbon\Carbon;
use Grr\GrrBundle\Entity\Periodicity;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodicityValidator extends ConstraintValidator
{
    /**
     * @param \Grr\GrrBundle\Entity\Periodicity|null           $value
     * @param \Grr\GrrBundle\Validator\Periodicity\Periodicity $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Periodicity) {
            throw new InvalidArgumentException($value, 0);
        }

        $typePeriodicity = $value->getType();

        if (null === $typePeriodicity || 0 === $typePeriodicity) {
            return;
        }

        $endPeriodicity = Carbon::instance($value->getEndTime());
        $entry = $value->getEntryReference();
        $entryEndTime = Carbon::instance($entry->getEndTime());

        /*
         * La date de fin de la periodicité doit être plus grande que la date de fin de la réservation
         */
        if ($endPeriodicity->format('Y-m-d') <= $entryEndTime->format('Y-m-d')) {
            $this->context->buildViolation('periodicity.constraint.end_time')
                ->addViolation();

            return;
        }
    }
}
