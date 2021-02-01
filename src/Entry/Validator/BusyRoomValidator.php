<?php

namespace Grr\GrrBundle\Entry\Validator;

use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BusyRoomValidator extends ConstraintValidator
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(EntryRepositoryInterface $entryRepository)
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
        //todo display entries conflit
        if (count($entries) > 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
