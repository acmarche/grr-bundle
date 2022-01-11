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

use Grr\Core\Tests\BaseTesting;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\Voter\EntryVoter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class EntryVoterTest extends BaseTesting
{
    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, array $datas): void
    {
        $this->loadFixtures();
        $voter = $this->initVoter();

        foreach ($datas as $data) {
            $entryName = $data[0];
            $result = $data[1];
            $email = $data[2];

            $area = $this->getEntry($entryName);
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
            EntryVoter::INDEX,
            [
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    null,
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'alice@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'fred@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            EntryVoter::EDIT,
            [
                [
                    'Réunion cst',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'alice@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_DENIED,
                    'fred@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];

        yield [
            EntryVoter::DELETE,
            [
                [
                    'Réunion cst',
                    Voter::ACCESS_DENIED,
                    null,
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'bob@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'alice@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'raoul@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_DENIED,
                    'fred@domain.be',
                ],
                [
                    'Réunion cst',
                    Voter::ACCESS_GRANTED,
                    'grr@domain.be',
                ],
            ],
        ];
    }

    private function initVoter(): EntryVoter
    {
        $container = $this->createMock(ContainerInterface::class);
        $security = new Security($container);

        //$mock = $this->createMock(AccessDecisionManager::class);

        return new EntryVoter($security, $this->initSecurityHelper());
    }

    private function initToken(?User $user): \AnonymousToken|UsernamePasswordToken
    {
        $token = $this->getMockBuilder(TokenInterface::class)->getMock(
        );

        $token
            ->expects($this->any())
            ->method('isAuthenticated')
            ->willReturn(true);

        if (null !== $user) {
            return new UsernamePasswordToken(
                $user,
                'homer',
                'app_user_provider'
            );
        }

        return $token = new AnonymousToken('secret', 'anonymous');
    }

    protected function loadFixtures(): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
                $this->pathFixtures.'room.yaml',
                $this->pathFixtures.'user.yaml',
                $this->pathFixtures.'authorization.yaml',
                $this->pathFixtures.'entry_type.yaml',
                $this->pathFixtures.'entry.yaml',
            ];

        $this->loader->load($files);
    }
}
