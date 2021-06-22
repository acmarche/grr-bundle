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
    private CanEdit $canEdit;

    public function __construct(CanEdit $canEdit)
    {
        $this->canEdit = $canEdit;
    }

    public function handle(Entry $post, User $user): bool
    {
        if ($this->canEdit->handle($post, $user)) {
            return true;
        }

        return $post->isPrivate();
    }
}
