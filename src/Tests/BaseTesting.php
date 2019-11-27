<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 20/08/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use DateTime;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\EntryType;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entity\Security\User;
use Grr\Core\Faker\CarbonProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Fidry\AliceDataFixtures\LoaderInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTesting extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    /**
     * @var LoaderInterface
     */
    protected $loader;
    /**
     * @var NativeLoader
     */
    protected $loaderSimple;
    /**
     * @var string
     */
    protected $pathFixtures;
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel2;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $administrator;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $bob;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    protected $brenda;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->kernel2 = self::bootKernel();

        $this->entityManager = $this->kernel2->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->pathFixtures = $this->kernel2->getProjectDir().'/src/Fixtures/';
        $this->loader = $this->kernel2->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $loader = new NativeLoader();
        $faker = $loader->getFakerGenerator();
        $faker->addProvider(CarbonProvider::class);

        $this->administrator = $this->createGrrClient('grr@domain.be');

        parent::setUp();
    }

    protected function createGrrClient(string $email, string $password = 'homer'): KernelBrowser
    {
        return static::createClient(
            [],
            [
                'PHP_AUTH_USER' => $email,
                'PHP_AUTH_PW' => $password,
            ]
        );
    }

    protected function getArea(string $name): ?object
    {
        return $this->entityManager
            ->getRepository(Area::class)
            ->findOneBy(['name' => $name]);
    }

    protected function getRoom(string $roomName): ?object
    {
        return $this->entityManager
            ->getRepository(Room::class)
            ->findOneBy(['name' => $roomName]);
    }

    protected function getPeriodicity(int $type, string $endTime): ?object
    {
        $dateTime = DateTime::createFromFormat('Y-m-d', $endTime);

        return $this->entityManager
            ->getRepository(Periodicity::class)
            ->findOneBy(['type' => $type, 'endTime' => $dateTime]);
    }

    protected function getEntry(string $name): ?object
    {
        return $this->entityManager
            ->getRepository(Entry::class)
            ->findOneBy(['name' => $name]);
    }

    protected function getUser(string $email): ?object
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    protected function getTypeEntry(string $name): ?object
    {
        return $this->entityManager
            ->getRepository(EntryType::class)
            ->findOneBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $purger = new ORMPurger($this->entityManager);
        //$purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();

        $this->kernel2->shutdown();
        $this->kernel2 = null;

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
