<?php

namespace Grr\GrrBundle\User\Command;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\Core\Password\PasswordHelper;
use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\User\Factory\UserFactory;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'grr:create-user',
    description: 'Création d\'un utilisateur',
)]
class CreateuserCommand extends Command
{
    public function __construct(
        private readonly UserFactory $userFactory,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHelper $passwordHelper
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $role = SecurityRole::ROLE_GRR_ADMINISTRATOR;

        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $symfonyStyle->error('Adresse email non valide');

            return 1;
        }

        if (\strlen((string) $name) < 1) {
            $symfonyStyle->error('Name minium 1');

            return 1;
        }

        if (!$password) {
            $question = new Question("Choisissez un mot de passe: \n");
            $question->setHidden(true);
            $question->setMaxAttempts(5);
            $question->setValidator(
                static function ($password) : string {
                    if (\strlen((string) $password) < 4) {
                        throw new RuntimeException('Le mot de passe doit faire minimum 4 caractères');
                    }
                    return $password;
                }
            );
            $password = $helper->ask($input, $output, $question);
        }

        if (null !== $this->userRepository->findOneBy([
                'email' => $email,
            ])) {
            $symfonyStyle->error('Un utilisateur existe déjà avec cette adresse email');

            return 1;
        }

        $confirmationQuestion = new ConfirmationQuestion("Administrateur de Grr ? [Y,n] \n", true);
        $administrator = $helper->ask($input, $output, $confirmationQuestion);

        $user = $this->userFactory->createNew();
        $user->setEmail($email);
        $user->setUsername($email);
        $user->setName($name);
        $user->setPassword($this->passwordHelper->encodePassword($user, $password));

        if ($administrator) {
            $user->addRole($role);
        }

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        $symfonyStyle->success("L'utilisateur a bien été créé");

        return Command::SUCCESS;
    }
}
