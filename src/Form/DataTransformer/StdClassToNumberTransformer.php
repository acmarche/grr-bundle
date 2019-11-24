<?php

namespace Grr\GrrBundle\Form\DataTransformer;

use stdClass;
use Grr\Core\Security\SecurityRole;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StdClassToNumberTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (area) to a string (number).
     *
     * @param int|null $value
     *
     * @return string|\stdClass
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        $roles = SecurityRole::getRolesForAuthorization();

        return $roles[$value];
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform the requests tainted data
     * into an acceptable format.
     *
     * The same transformers are called in the reverse order so the responsibility is to
     * return one of the types that would be expected as input of transform().
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param \stdClass $value The value in the transformed representation
     *
     * @throws TransformationFailedException when the transformation fails
     *
     * @return mixed|null
     */
    public function reverseTransform($value)
    {
        if ($value instanceof stdClass) {
            return $value->value;
        }

        return null;
    }
}
