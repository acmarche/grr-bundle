<?php

namespace Grr\GrrBundle\Command;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Security\PasswordHelper;
use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Security\Factory\UserFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateuserCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'grr:create-user';
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var PasswordHelper
     */
    private $passwordHelper;

    public function __construct(
        UserFactory $userFactory,
        UserRepositoryInterface $userRepository,
        PasswordHelper $passwordHelper
    ) {
        parent::__construct();
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->passwordHelper = $passwordHelper;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Création d\'un utilisateur')
            ->addArgument('name', InputArgument::REQUIRED, 'Name')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $role = SecurityRole::ROLE_GRR_ADMINISTRATOR;

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $io->error('Adresse email non valide');

            return 1;
        }

        if (strlen($name) < 1) {
            $io->error('Name minium 1');

            return 1;
        }

        if (!$password) {
            $question = new Question("Choisissez un mot de passe: \n");
            $question->setHidden(true);
            $question->setMaxAttempts(5);
            $question->setValidator(
                function ($password): string {
                    if (strlen($password) < 4) {
                        throw new RuntimeException('Le mot de passe doit faire minimum 4 caractères');
                    }

                    return $password;
                }
            );
            $password = $helper->ask($input, $output, $question);
        }

        if ($this->userRepository->findOneBy(['email' => $email]) !== null) {
            $io->error('Un utilisateur existe déjà avec cette adresse email');

            return 1;
        }

        $questionAdministrator = new ConfirmationQuestion("Administrateur de Grr ? [Y,n] \n", true);
        $administrator = $helper->ask($input, $output, $questionAdministrator);

        $user = $this->userFactory->createNew();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setName($name);
        $user->setPassword($this->passwordHelper->encodePassword($user, $password));

        if ($administrator) {
            $user->addRole($role);
        }

        $this->userManager->insert($user);

        $io->success("L'utilisateur a bien été créé");

        return 0;
    }
}
