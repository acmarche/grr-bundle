<?php

namespace Grr\GrrBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FixCommand extends Command
{
    protected static $defaultName = 'app:fix';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $value = Yaml::parseFile('/var/www/grr5/src/Grr/GrrBundle/translations/messages.fr.yaml');
            ksort($value);

            $yaml = Yaml::dump($value, 4);
            file_put_contents('/var/www/grr5/src/Grr/GrrBundle/translations/messages22.fr.yaml', $yaml);
        } catch (ParseException $exception) {
            $io->writeln('error: %s', $exception->getMessage());
        }
        return 0;
    }
}
