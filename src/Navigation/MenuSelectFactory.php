<?php

namespace Grr\GrrBundle\Navigation;

class MenuSelectFactory
{
    public function createNew(): MenuSelect
    {
        return new MenuSelect();
    }
}
