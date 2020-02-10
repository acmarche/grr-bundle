<?php

namespace Grr\GrrBundle\Validator\Entry;

use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\GrrBundle\Repository\EntryRepository;
use InvalidArgumentException;
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
     * @param EntryInterface $value
     * @param BusyRoom       $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof EntryInterface) {
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
