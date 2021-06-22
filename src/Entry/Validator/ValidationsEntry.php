<?php

namespace Grr\GrrBundle\Entry\Validator;

use Grr\GrrBundle\Entity\Entry;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationsEntry
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return ConstraintViolationListInterface[]
     */
    public function validate(Entry $entry): array
    {
        $violations = [];
        $validators = $this->getValidators();
        foreach ($validators as $validator) {
            $constraint = new $validator();
            $violations[] = $this->validator->validate($entry, $constraint);
        }

        return $violations;
    }

    /**
     * @return string[]
     */
    protected function getValidators(): array
    {
        return [BusyRoom::class];
    }
}
