<?php

namespace Grr\GrrBundle\Navigation;

class MenuSelectFactory
{
    public function createNew(): MenuSelectDto
    {
        return new MenuSelectDto();
    }
}
