<?php
/**
 * This file is part of grr5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 21/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Grr\Core\Contrat\Entity\Security\UserInterface;
use Grr\Core\User\Entity\UserTrait;
use Grr\GrrBundle\User\Repository\UserRepository;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as UserInterfaceSf;

#[ORM\Table(name: 'user')]
#[ORM\UniqueConstraint(columns: ['email', 'username'])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un utilisateur a déjà cette adresse email')]
#[UniqueEntity(fields: ['username'], message: "Un utilisateur a déjà ce nom d'utilisateur")]
class User implements UserInterface, UserInterfaceSf, TimestampableInterface, PasswordAuthenticatedUserInterface
{
    use UserTrait;

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getSalt(): void
    {
    }
}
