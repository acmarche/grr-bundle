<?php

namespace Grr\GrrBundle\Room\DataTransformer;

use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\GrrBundle\Entity\Area;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NumberToRoomTransformer implements DataTransformerInterface
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {
    }

    /**
     * Transforms an object (area) to a string (number).
     *
     * @param Area|null $area
     *
     * @return string|int|null
     */
    public function transform($area)
    {
        if (null === $area) {
            return '';
        }

        return $area->getId();
    }

    /**
     * Transforms a string (number) to an object (area).
     *
     * @param string $areaNumber
     *
     * @return Area|null
     *
     * @throws TransformationFailedException if object (area) is not found
     */
    public function reverseTransform($areaNumber)
    {
        // no area number? It's optional, so that's ok
        if ('' === $areaNumber) {
            return null;
        }

        $room = $this->roomRepository->find($areaNumber);

        if (null === $room) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf('An area with number "%s" does not exist!', $areaNumber));
        }

        return $room;
    }
}
