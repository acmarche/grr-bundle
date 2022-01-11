<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 21/08/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Fixtures;

use Fidry\AliceDataFixtures\ProcessorInterface;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess(string $fixtureId, $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(string $fixtureId, $user): void
    {
        // do nothing
    }
}
