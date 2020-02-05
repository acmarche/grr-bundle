<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 6/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Tests\Security;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Security\Authorization;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Security\SecurityHelper;
use Grr\GrrBundle\Tests\BaseTesting;

class SecurityHelperTest extends BaseTesting
{
    /**
     * @dataProvider provideAdministrator
     */
    public function testIsAdministrator(
        string $email,
        bool $access1,
        bool $access2,
        bool $access3,
        bool $access4
    ): void {
        $this->loadFixtures();

        $area = $this->getArea('Esquare');
        $securityHelper = $this->initSecurityHelper();
        $user = $this->getUser($email);

        self::assertSame($access1, $securityHelper->isAreaAdministrator($user, $area));

        $room = $this->getRoom('Box');
        self::assertSame($access2, $securityHelper->isRoomAdministrator($user, $room));

        $room = $this->getRoom('Salle cafétaria');
        self::assertSame($access3, $securityHelper->isRoomAdministrator($user, $room));

        $area = $this->getArea('Hdv');
        self::assertSame($access4, $securityHelper->isAreaAdministrator($user, $area));
    }

    public function provideAdministrator(): iterable
    {
        yield 'administrator' => [
            'bob@domain.be',
            true,
            true,
            false,
            false,
        ];

        yield 'not admin' => [
            'alice@domain.be',
            false,
            false,
            false,
            false,
        ];

        yield 'admin area of Hdv' => [
            'joseph@domain.be',
            false,
            false,
            true,
            true,
        ];

        yield 'not admin area of Hdv' => [
            'kevin@domain.be',
            false,
            false,
            false,
            false,
        ];

        yield 'admin cafet' => [
            'fred@domain.be',
            false,
            false,
            true,
            false,
        ];

        yield 'not admin' => [
            'raoul@domain.be',
            false,
            true,
            false,
            false,
        ];

        yield 'box ' => [
            'charle@domain.be',
            false,
            false,
            false,
            false,
        ];
    }

    /**
     * @dataProvider provideManager
     */
    public function testIsManager(string $email, bool $access1, bool $access2, bool $access3, bool $access4): void
    {
        $this->loadFixtures();

        $securityHelper = $this->initSecurityHelper();
        $user = $this->getUser($email);

        $area = $this->getArea('Esquare');
        self::assertSame($access1, $securityHelper->isAreaManager($user, $area));

        $room = $this->getRoom('Box');
        self::assertSame($access2, $securityHelper->isRoomManager($user, $room));

        $room = $this->getRoom('Salle cafétaria');
        self::assertSame($access3, $securityHelper->isRoomManager($user, $room));

        $area = $this->getArea('Hdv');
        self::assertSame($access4, $securityHelper->isAreaManager($user, $area));
    }

    public function provideManager(): iterable
    {
        yield 'administrator' => [
            'bob@domain.be',
            true,
            true,
            false,
            false,
        ];

        yield 'not admin' => [
            'alice@domain.be',
            true,
            true,
            false,
            false,
        ];

        yield 'admin area of Hdv' => [
            'joseph@domain.be',
            false,
            false,
            true,
            true,
        ];

        yield 'not admin area of Hdv' => [
            'kevin@domain.be',
            false,
            false,
            true,
            true,
        ];

        yield 'admin cafet' => [
            'fred@domain.be',
            false,
            false,
            true,
            false,
        ];

        yield 'not admin' => [
            'raoul@domain.be',
            false,
            true,
            false,
            false,
        ];

        yield 'box ' => [
            'charle@domain.be',
            false,
            true,
            false,
            false,
        ];
    }

    /**
     * @dataProvider provideAddEntry
     */
    public function testAddEntry(string $email, bool $access1, bool $access2): void
    {
        $this->loadFixtures();

        $securityHelper = $this->initSecurityHelper();
        $user = $this->getUser($email);

        $room = $this->getRoom('Box');
        self::assertSame($access1, $securityHelper->canAddEntry($room, $user));

        $room = $this->getRoom('Salle cafétaria');
        self::assertSame($access2, $securityHelper->canAddEntry($room, $user));
    }

    public function provideAddEntry(): iterable
    {
        yield 'administrator' => [
            'bob@domain.be',
            true,
            false,
        ];

        yield 'not admin' => [
            'alice@domain.be',
            true,
            false,
        ];

        yield 'admin area of Hdv' => [
            'joseph@domain.be',
            false,
            true,
        ];

        yield 'not admin area of Hdv' => [
            'kevin@domain.be',
            false,
            true,
        ];

        yield 'admin cafet' => [
            'fred@domain.be',
            false,
            true,
        ];

        yield 'box admin' => [
            'raoul@domain.be',
            true,
            false,
        ];

        yield 'box ' => [
            'charle@domain.be',
            true,
            false,
        ];
    }

    /**
     * @dataProvider provideRoom
     *
     * @param string $email
     * @param bool   $access1
     * @param bool   $access2
     */
    public function testAddEntryWithRule(string $name, array $users): void
    {
        $this->loadFixtures(true);
        $securityHelper = $this->initSecurityHelper();
        $room = $this->getRoom($name);

        foreach ($users as $data) {
            $user = $this->getUser($data[0]);
            $access = $data[1];
            self::assertSame($access, $securityHelper->canAddEntry($room, $user), $user->getEmail().' for '.$name);
        }
    }

    public function provideRoom(): iterable
    {
        /*
         * every body
         */
        yield [
            'Room 1',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    true,
                ],
                [
                    'joseph@domain.be',
                    true,
                ],
                [
                    'raoul@domain.be',
                    true,
                ],
                [
                    'kevin@domain.be',
                    true,
                ],
                [
                    'fred@domain.be',
                    true,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every connected
         */
        yield [
            'Room 2',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    true,
                ],
                [
                    'joseph@domain.be',
                    true,
                ],
                [
                    'raoul@domain.be',
                    true,
                ],
                [
                    'kevin@domain.be',
                    true,
                ],
                [
                    'fred@domain.be',
                    true,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every actif user
         */
        yield [
            'Room 3',
            [
                [
                    'bob@domain.be',
                    false,
                ],
                [
                    'alice@domain.be',
                    false,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    false,
                ],
                [
                    'kevin@domain.be',
                    false,
                ],
                [
                    'fred@domain.be',
                    true,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every room administrator
         */
        yield [
            'Room 4',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    false,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    true,
                ],
                [
                    'kevin@domain.be',
                    false,
                ],
                [
                    'fred@domain.be',
                    false,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every room manager
         */
        yield [
            'Room 5',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    true,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    false,
                ],
                [
                    'kevin@domain.be',
                    true,
                ],
                [
                    'fred@domain.be',
                    false,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every area administator
         */
        yield [
            'Room 6',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    false,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    false,
                ],
                [
                    'kevin@domain.be',
                    false,
                ],
                [
                    'fred@domain.be',
                    false,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every area manager
         */
        yield [
            'Room 7',
            [
                [
                    'bob@domain.be',
                    true,
                ],
                [
                    'alice@domain.be',
                    true,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    false,
                ],
                [
                    'kevin@domain.be',
                    false,
                ],
                [
                    'fred@domain.be',
                    false,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];

        /*
         * every GRR_ADMINISTRATOR
         */
        yield [
            'Room 9',
            [
                [
                    'bob@domain.be',
                    false,
                ],
                [
                    'alice@domain.be',
                    false,
                ],
                [
                    'joseph@domain.be',
                    false,
                ],
                [
                    'raoul@domain.be',
                    false,
                ],
                [
                    'kevin@domain.be',
                    false,
                ],
                [
                    'grr@domain.be',
                    true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideGrrAdministrator
     */
    public function testIsGrrAdministrator(User $user, bool $administrator): void
    {
        $this->loadFixtures();
        $securityHelper = $this->initSecurityHelper();

        self::assertSame($administrator, $securityHelper->isGrrAdministrator($user));
    }

    /**
     * @return \Grr\GrrBundle\Entity\Security\User[][]|bool[][]
     */
    public function provideGrrAdministrator(): iterable
    {
        $user = new User();
        $user->addRole(SecurityRole::ROLE_GRR_ADMINISTRATOR);

        yield 'administrator' => [
            $user,
            true,
        ];

        $user = new User();
        $user->setRoles(SecurityRole::getRoles());
        $user->removeRole(SecurityRole::ROLE_GRR_ADMINISTRATOR);

        yield 'not administrator' => [
            $user,
            false,
        ];

        yield 'not administrator' => [
            new User(),
            false,
        ];
    }

    protected function initSecurityHelper(): SecurityHelper
    {
        return new SecurityHelper($this->entityManager->getRepository(Authorization::class));
    }

    protected function loadFixtures($rule = false): void
    {
        $files =
            [
                $this->pathFixtures.'area.yaml',
            ];

        if ($rule) {
            $files[] = $this->pathFixtures.'room_with_rule.yaml';
            $files[] = $this->pathFixtures.'authorization_rule.yaml';
            $files[] = $this->pathFixtures.'user_rule.yaml';
        } else {
            $files[] = $this->pathFixtures.'room.yaml';
            $files[] = $this->pathFixtures.'user.yaml';
            $files[] = $this->pathFixtures.'authorization.yaml';
        }

        $this->loader->load($files);
    }
}
