<?php

namespace Grr\GrrBundle\Periodicity\Validator;

use Carbon\Carbon;
use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Entity\Periodicity;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodicityEveryWeekValidator extends ConstraintValidator
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

        if (PeriodicityConstant::EVERY_WEEK !== $value->getType()) {
            return;
        }

        $endPeriodicity = Carbon::instance($value->getEndTime());
        $entry = $value->getEntryReference();
        $entryEndTime = Carbon::instance($entry->getEndTime());
        $daysSelected = $value->getWeekDays();
        $weekRepeat = $value->getWeekRepeat();
        $entryStartTime = Carbon::instance($entry->getStartTime());

        /*
         * Si aucun jour de la semaine sélectionné
         */
        if (count($daysSelected) < 1) {
            $this->context->buildViolation('constraint.periodicity.every_weeks.no_day')
                ->addViolation();
        }

        /*
         * Si aucune répétion par semaine choisie
         */
        if (null === $weekRepeat) {
            $this->context->buildViolation('constraint.periodicity.every_weeks.no_repeat')
                ->addViolation();
        }

        /**
         * En répétition par semain, il y doit y avoir au moins $weekRepeat semaine de différence entre la fin de la périodicité
         * et la fin de la réservation.
         */
        if ($entryEndTime->diffInWeeks($endPeriodicity) < $weekRepeat) {
            $this->context->buildViolation('constraint.periodicity.every_weeks.endtime')
                ->addViolation();

            return;
        }

        /*
         * En répétion par semaine, la réservation ne peut s'étaler sur plusieurs jours
         */
        if ($entryStartTime->diffInDays($entryEndTime) > 1) {
            $this->context->buildViolation('constraint.periodicity.every_weeks.more24h')
                ->addViolation();
        }
    }
}
