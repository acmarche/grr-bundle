<?php

namespace Grr\GrrBundle\Navigation;

use Grr\GrrBundle\Model\Navigation;

class NavigationFactory
{
    public function createNew(): Navigation
    {
        return new Navigation();
    }
}
