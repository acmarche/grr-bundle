<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 6/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Tests\Security\Voter;

use Grr\GrrBundle\Entity\Security\Authorization;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\SecurityHelper;
use Grr\GrrBundle\Security\Voter\RoomVoter;
use Grr\GrrBundle\Tests\BaseTesting;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoomVoterTest extends BaseTesting
{
    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, array $datas): void
    {
        $this->loadFixtures();
        $voter = $this->initVoter();

        foreach ($datas as $data) {
            $roomName = $data[0];
            $result = $data[1];
            $email = $data[2];

            $area = $this->getRoom($roomName);
            $user = null;
            if ($email) {
                $user = $this->getUser($email);
            }

            $token = $this->initToken($user);
            $this->assertSame($result, $voter->vote($token, $area, [$attribute]));
        }
    }

    public function provideCases(): iterable
    {
        yield [
            RoomVoter::INDEX,
            [
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'alice@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'fred@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            RoomVoter::EDIT,
            [
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    'alice@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    'fred@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            RoomVoter::DELETE,
            [
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    'alice@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    'fred@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            RoomVoter::ADD_ENTRY,
            [
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'alice@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_DENIED,
                    'fred@domain.be',
                ],
                [
                    'Box',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];
    }

    protected function initSecurityHelper(): SecurityHelper
    {
        return new SecurityHelper($this->entityManager->getRepository(Authorization::class));
    }

    private function initVoter(): RoomVoter
    {
        $mock = $this->createMock(AccessDecisionManager::class);

        return new RoomVoter($mock, $this->initSecurityHelper());
    }

    private function initToken(?User $user): TokenInterface
    {
        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')->getMock(
        );

        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->willReturn(true);

        $token = new AnonymousToken('secret', 'anonymous');
        if (null !== $user) {
            $token = new UsernamePasswordToken(
                $user, 'homer', 'app_user_provider'
            );
        }

        return $token;
    }

    protected function loadFixtures(): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
                $this->pathFixtures.'room.yaml',
                $this->pathFixtures.'user.yaml',
                $this->pathFixtures.'authorization.yaml',
            ];

        $this->loader->load($files);
    }
}
