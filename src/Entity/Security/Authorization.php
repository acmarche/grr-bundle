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
use Grr\Core\Contrat\Entity\Security\AuthorizationInterface;
use Grr\Core\Entity\Security\AuthorizationTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Authorization.
 *
 * @ORM\Table(name="authorization", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"user_id", "area_id"}),
 *     @ORM\UniqueConstraint(columns={"user_id", "room_id"})
 * })
 * @ORM\Entity(repositoryClass="Grr\GrrBundle\Repository\Security\AuthorizationRepository")
 * @UniqueEntity(fields={"user", "area"}, message="Ce user est déjà lié au domaine")
 * @UniqueEntity(fields={"user", "room"}, message="Ce user est déjà lié à la room")
 */
class Authorization implements AuthorizationInterface, TimestampableInterface
{
    use AuthorizationTrait;
}
