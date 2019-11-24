<?php

namespace Grr\GrrBundle\Validator;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Validator\Entry\BusyRoom;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationsEntry
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface[]
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
