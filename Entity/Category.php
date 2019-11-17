<?php

namespace Grr\GrrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Doctrine\Traits\IdEntityTrait;
use Grr\Core\Doctrine\Traits\NameEntityTrait;
use Grr\Core\Entity\BaseCategory;
use Grr\Core\Entity\LoloTrait;

/**
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\CategoryRepository")
 */
class Category extends BaseCategory
{
    use IdEntityTrait;
    use LoloTrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;


}
