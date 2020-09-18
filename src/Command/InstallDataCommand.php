<?php

namespace Grr\GrrBundle\Command;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\RoomRepositoryInterface;
use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\Core\Security\SecurityRole;
use Grr\Core\Setting\SettingConstants;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Room\Factory\RoomFactory;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Grr\GrrBundle\Setting\Factory\SettingFactory;
use Grr\GrrBundle\Setting\Repository\SettingRepository;
use Grr\GrrBundle\TypeEntry\Repository\TypeEntryRepository;
use Grr\GrrBundle\TypeEntry\TypeEntryFactory;
use Grr\GrrBundle\User\Factory\UserFactory;
use Grr\GrrBundle\User\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InstallDataCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'grr:install-data';
    /**
     * @var TypeEntryRepository
     */
    private $typeEntryRepository;
    /**
     * @var TypeEntryFactory
     */
    private $typeEntryFactory;
    /**
     * @var AreaRepository
     */
    private $areaRepository;
    /**
     * @var AreaFactory
     */
    private $areaFactory;
    /**
     * @var RoomFactory
     */
    private $roomFactory;
    /**
     * @var RoomRepository
     */
    private $roomRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var SettingFactory
     */
    private $settingFactory;
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TypeEntryRepositoryInterface $typeEntryRepository,
        RoomRepositoryInterface $roomRepository,
        UserRepositoryInterface $userRepository,
        SettingRepositoryInterface $settingRepository,
        TypeEntryFactory $typeEntryFactory,
        AreaRepositoryInterface $areaRepository,
        SettingFactory $settingFactory,
        AreaFactory $areaFactory,
        RoomFactory $roomFactory,
        UserFactory $userFactory,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        parent::__construct();
        $this->typeEntryRepository = $typeEntryRepository;
        $this->typeEntryFactory = $typeEntryFactory;
        $this->areaRepository = $areaRepository;
        $this->areaFactory = $areaFactory;
        $this->roomFactory = $roomFactory;
        $this->roomRepository = $roomRepository;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->settingFactory = $settingFactory;
        $this->settingRepository = $settingRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Initialize les données dans la base de données lors de l\'installation')
            ->addArgument('purge', InputArgument::OPTIONAL, 'purger les tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle = new SymfonyStyle($input, $output);
        $purge = false;

        if (!$input->getArgument('purge')) {
            $helper = $this->getHelper('question');
            $confirmationQuestion = new ConfirmationQuestion("Voulez vous vider la base de données ? [y,N] \n", false);
            $purge = $helper->ask($input, $output, $confirmationQuestion);
        } else {
            $purge = true;
        }

        if ($purge) {
            $ormPurger = new ORMPurger($this->entityManager);
            $ormPurger->purge();

            $this->symfonyStyle->success('La base de données a bien été vidée.');
        }

        $this->loadType();
        $this->loadArea();
        $this->loadUser();
        $this->loadSetting();

        $this->symfonyStyle->success('Les données ont bien été initialisées.');

        return 0;
    }

    public function loadType(): void
    {
        $types = [
            'A' => 'Cours',
            'B' => 'Reunion',
            'C' => 'Location',
            'D' => 'Bureau',
            'E' => 'Mise a disposition',
            'F' => 'Non disponible',
        ];

        $colors = ['#FFCCFF', '#99CCCC', '#FF9999', '#FFFF99', '#C0E0FF', '#FFCC99', '#FF6666', '#66FFFF', '#DDFFDD'];

        foreach ($types as $index => $nom) {
            if (null !== $this->typeEntryRepository->findOneBy(['name' => $nom])) {
                continue;
            }
            $type = $this->typeEntryFactory->createNew();
            $type->setLetter($index);
            $type->setName($nom);
            $type->setColor($colors[random_int(0, count($colors) - 1)]);
            $this->entityManager->persist($type);
        }
        $this->entityManager->flush();
    }

    /**
     * @return null
     */
    public function loadArea()
    {
        $esquareName = 'Esquare';
        $esquare = $this->areaRepository->findOneBy(['name' => $esquareName]);
        if (null === $esquare) {
            $esquare = $this->areaFactory->createNew();
            $esquare->setName($esquareName);
            $this->entityManager->persist($esquare);
        }

        $hdvName = 'Hdv';
        $hdv = $this->areaRepository->findOneBy(['name' => $hdvName]);
        if (null === $hdv) {
            $hdv = $this->areaFactory->createNew();
            $hdv->setName($hdvName);
            $this->entityManager->persist($hdv);
        }

        $salles = [
            'Box',
            'Créative',
            'Meeting Room',
            'Relax Room',
            'Digital Room',
        ];

        $this->loadRooms($esquare, $salles);

        $salles = [
            'Salle du Conseil',
            'Salle du Collège',
            'Salle cafétaria',
        ];

        $this->loadRooms($hdv, $salles);

        $this->entityManager->flush();

        return null;
    }

    public function loadRooms(Area $area, array $salles): void
    {
        foreach ($salles as $salle) {
            if (null !== $this->roomRepository->findOneBy(['name' => $salle])) {
                continue;
            }
            $room = $this->roomFactory->createNew($area);
            $room->setName($salle);
            $this->entityManager->persist($room);
        }
    }

    public function loadUser(): void
    {
        $email = 'grr@domain.be';

        if (null !== $this->userRepository->findOneBy(['email' => $email])) {
            return;
        }

        $password = 'homer'; //todo remove
        $roleGrrAdministrator = SecurityRole::ROLE_GRR_ADMINISTRATOR;

        $user = $this->userFactory->createNew();
        $user->setName('Administrator');
        $user->setFirstName('Grr');
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $password));
        $user->addRole($roleGrrAdministrator);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->symfonyStyle->success("L'utilisateur $email avec le mot de passe $password a bien été créé");
    }

    private function loadSetting(): void
    {
        $settings = [
            SettingConstants::WEBMASTER_EMAIL => ['grr@domain.be'],
            SettingConstants::WEBMASTER_NAME => 'Grr',
            SettingConstants::TECHNICAL_SUPPORT_EMAIL => ['grr@domain.be'],
            SettingConstants::MESSAGE_HOME_PAGE => 'Message home page',
            SettingConstants::TITLE_HOME_PAGE => 'Gestion et Réservation des salles',
            SettingConstants::COMPANY => 'Grr',
            SettingConstants::NB_CALENDAR => 1,
            SettingConstants::DEFAULT_LANGUAGE => 'fr',
        ];

        foreach ($settings as $name => $value) {
            if (null === ($setting = $this->settingRepository->findOneBy(['name' => $name]))) {
                if (is_array($value)) {
                    $value = serialize($value);
                }
                $setting = $this->settingFactory->createNew($name, $value);
                $this->entityManager->persist($setting);
            }
        }

        $this->entityManager->flush();
    }
}
