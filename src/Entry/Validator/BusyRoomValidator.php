<?php

namespace Grr\GrrBundle\Entry\Validator;

use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusyRoomValidator extends ConstraintValidator
{
    public function __construct(
        private EntryRepositoryInterface $entryRepository
    ) {
    }

    /**
     * @param EntryInterface $value
     * @param BusyRoom       $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $value instanceof EntryInterface) {
            throw new InvalidArgumentException($value, 0);
        }

        $room = $value->getRoom();

        $entries = $this->entryRepository->isBusy($value, $room);

        if ([] !== $entries) {
            $message = '';
            foreach ($entries as $entry) {
                $message .= ' '.$entry->getName();
            }
            $this->context->buildViolation($constraint->message)
                ->setParameters([
                    'rooms' => $message,
                ])
                ->addViolation();
        }
    }
}
