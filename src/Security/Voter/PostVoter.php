<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Security\User;
use LogicException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * A utiliser ainsi dans controller
 * $this->denyAccessUnlessGranted(CanView::class, $entry);
 * Class PostVoter.
 */
class PostVoter extends Voter
{
    public function __construct(
        private ServiceLocator $criteria
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        /*   dump($attribute);
           dump($this->criteria->getProvidedServices());
           dump($this->criteria->has($attribute));*/

        return $subject instanceof Entry && $this->criteria->has($attribute);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        /** @var Entry $post */
        $post = $subject;

        /** @var CriterionInterface $criterion */
        $criterion = ($this->criteria)($attribute);
        $criterion = $this->criteria->get($attribute);

        if ($criterion) {
            return $criterion->handle($post, $user);
        }

        throw new LogicException('This code should not be reached!');
    }
}
