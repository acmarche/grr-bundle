<?php
/**
 * This file is part of grr5 application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 21/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Grr\GrrBundle\Entity\Security;

use Grr\Core\Entity\Security\UserInterface;
use Grr\Core\Entity\Security\UserTrait;
use Symfony\Component\Security\Core\User\UserInterface as UserInterfaceSf;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 *     @ORM\UniqueConstraint(columns={"username"})
 * })
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\Security\UserRepository")
 * @UniqueEntity(fields={"email"}, message="Un utilisateur a déjà cette adresse email")
 * @UniqueEntity(fields={"username"}, message="Un utilisateur a déjà ce nom d'utilisateur")
 */
class User implements UserInterface, UserInterfaceSf
{
    use UserTrait;
}