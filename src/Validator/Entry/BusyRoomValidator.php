<?php

namespace Grr\GrrBundle\Validator\Entry;

use InvalidArgumentException;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Repository\EntryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusyRoomValidator extends ConstraintValidator
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(EntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param Entry    $value
     * @param BusyRoom $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof Entry) {
            throw new InvalidArgumentException($value, 0);
        }

        $room = $value->getRoom();

        $entries = $this->entryRepository->isBusy($value, $room);

        if (count($entries) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
