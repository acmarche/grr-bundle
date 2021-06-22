<?php

namespace Grr\GrrBundle\Entry\Validator;

use Exception;
use Grr\Core\Model\DurationModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Uniquement un float pour les heures
 * Class DurationValidator.
 */
class DurationValidator extends ConstraintValidator
{
    /**
     * @param DurationModel $value
     * @param Duration      $constraint
     *
     * @throws Exception
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof DurationModel) {
            throw new Exception('Not valid value');
        }

        $unit = $value->getUnit();
        $time = $value->getTime();
        $whole = (int) ($time);
        $fraction = $time - $whole;

        if (DurationModel::UNIT_TIME_HOURS !== $unit && 0.0 !== $fraction) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
