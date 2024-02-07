<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Security\User;

/**
 * A utiliser ainsi dans controller
 * $this->denyAccessUnlessGranted(CanView::class, $entry);
 * Class CanView.
 */
class CanView implements CriterionInterface
{
    public function __construct(
        private readonly CanEdit $canEdit
    ) {
    }

    public function handle(Entry $post, User $user): bool
    {
        if ($this->canEdit->handle($post, $user)) {
            return true;
        }

        return $post->isPrivate();
    }
}
