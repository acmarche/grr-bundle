<?php

namespace Grr\GrrBundle\Command;

use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Notification\FlashNotification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FixCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:fix';
    private NotifierInterface $notifier;
    private EntryRepository $entryRepository;

    public function __construct(NotifierInterface $notifier, EntryRepository $entryRepository, string $name = null)
    {
        parent::__construct($name);
        $this->notifier = $notifier;
        $this->entryRepository = $entryRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $noti = new Notification('super', ['email']);
        $noti->content('coucou');
        $noti->importance('low');

        $entry = $this->entryRepository->find(2818);
        $notification = new FlashNotification('super', 'danger');

        $recipient = new Recipient(
            'jf@marche.be',
        );

        $this->notifier->send($notification, ...$this->notifier->getAdminRecipients());
        $this->notifier->send($notification);
        $this->notifier->send($noti, $recipient);

        $symfonyStyle = new SymfonyStyle($input, $output);

        /*     try {
                 $value = Yaml::parseFile('/var/www/grr5/src/Grr/GrrBundle/translations/messages.fr.yaml');
                 ksort($value);

                 $yaml = Yaml::dump($value, 4);
                 file_put_contents('/var/www/grr5/src/Grr/GrrBundle/translations/messages22.fr.yaml', $yaml);
             } catch (ParseException $exception) {
                 $symfonyStyle->writeln('error: %s', $exception->getMessage());
             }
     */

        return 0;
    }
}
