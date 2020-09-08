<?php

namespace Grr\GrrBundle\Security\Voter;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Security\User;

interface CriterionInterface
{
    public function handle(Entry $post, User $user): bool;
}
