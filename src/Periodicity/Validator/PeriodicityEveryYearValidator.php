<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Carbon\Carbon;
use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Entity\Periodicity;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodicityEveryYearValidator extends ConstraintValidator
{
    /**
     * @param Periodicity|null $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof Periodicity) {
            throw new InvalidArgumentException($value, 0);
        }

        if (PeriodicityConstant::EVERY_YEAR !== $value->getType()) {
            return;
        }

        $endPeriodicity = Carbon::instance($value->getEndTime());
        $entry = $value->getEntryReference();
        $entryEndTime = Carbon::instance($entry->getEndTime());

        /*
         * En répétition par année, il y doit y avoir au moins un an de différence entre la fin de la périodicité
         * et la fin de la réservation
         */
        if ($entryEndTime->diffInYears($endPeriodicity) < 1) {
            $this->context->buildViolation('constraint.periodicity.every_year')
                ->addViolation();

            return;
        }
    }
}
