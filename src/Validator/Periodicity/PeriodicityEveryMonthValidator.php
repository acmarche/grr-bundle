<?php

namespace Grr\GrrBundle\Validator\Periodicity;

use Carbon\Carbon;
use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Entity\Periodicity;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodicityEveryMonthValidator extends ConstraintValidator
{
    /**
     * @param \Grr\GrrBundle\Entity\Periodicity|null $value
     * @param PeriodicityEveryWeek                   $constraint
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

        if (PeriodicityConstant::EVERY_MONTH_SAME_DAY !== $typePeriodicity && PeriodicityConstant::EVERY_MONTH_SAME_WEEK_DAY !== $typePeriodicity) {
            return;
        }

        $endPeriodicity = Carbon::instance($value->getEndTime());
        $entry = $value->getEntryReference();
        $entryEndTime = Carbon::instance($entry->getEndTime());

        /*
         * En répétition par mois, il y doit y avoir au moins un mois de différence entre la fin de la périodicité
         * et la fin de la réservation
         */
        if ($entryEndTime->diffInMonths($endPeriodicity) < 1) {
            $this->context->buildViolation('constraint.periodicity.every_month')
                ->addViolation();

            return;
        }
    }
}
