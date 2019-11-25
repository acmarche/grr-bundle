<?php

namespace Grr\GrrBundle\Navigation;

use Grr\Core\Model\Navigation;

class NavigationFactory
{
    public function createNew(): Navigation
    {
        return new Navigation();
    }
}
