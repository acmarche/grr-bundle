<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Carbon\Carbon;
use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Entity\Periodicity;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodicityEveryDayValidator extends ConstraintValidator
{
    /**
     * @param Periodicity|null $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        if (! $value instanceof Periodicity) {
            throw new InvalidArgumentException($value, 0);
        }

        if (PeriodicityConstant::EVERY_DAY !== $value->getType()) {
            return;
        }

        $endPeriodicity = Carbon::instance($value->getEndTime());
        $entry = $value->getEntryReference();
        $entryStartTime = Carbon::instance($entry->getStartTime());
        $entryEndTime = Carbon::instance($entry->getEndTime());

        /*
         * En répétion tous les jours, la réservation ne peut s'étaler sur plusieurs jours
         */
        if ($entryStartTime->diffInDays($entryEndTime) > 1) {
            $this->context->buildViolation('constraint.periodicity.every_day')
                ->addViolation();
        }

        /*
         * En répétition par jour, il y doit y avoir au moins un jour de différence entre la fin de la périodicité
         * et la fin de la réservation
         */
        if ($entryEndTime->diffInDays($endPeriodicity) < 1) {
            $this->context->buildViolation('constraint.periodicity.end_time')
                ->addViolation();

            return;
        }
    }
}
