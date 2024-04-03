<?php

namespace Grr\GrrBundle\Command;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'grr:migration',
    description: 'Add a short description for your command',
)]
class GrrCommand extends Command
{
    public function __construct(
        private readonly EntryRepository $entryRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pdo = new PDO('mysql:host=localhost;dbname=grr_ale', 'root', 'homer');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $symfonyStyle = new SymfonyStyle($input, $output);

        foreach ($this->entryRepository->findAll() as $entry) {
            $repeatId = 0;
            $data = [
                //  ':idold' => $entry->getId(),
                ':start_time' => $entry->getStartTime()->getTimestamp(),
                ':end_time' => $entry->getEndTime()->getTimestamp(),
                ':entry_type' => 0,
                ':type' => $entry->getType()->getLetter(),
                ':repeat_id' => $repeatId,
                ':room_id' => $this->getRoomId($entry),
                ':timestamp' => null,
                ':create_by' => $entry->getCreatedBy(),
                ':beneficiaire_ext' => $entry->getReservedFor() ?? $entry->getCreatedBy(),
                ':beneficiaire' => $entry->getReservedFor() ?? $entry->getCreatedBy(),
                ':name' => $entry->getName(),
                ':description' => $entry->getDescription(),
                ':statut_entry' =>  '-',
            ];
            $sql = 'INSERT INTO grr_entry(`start_time`,`end_time`,`entry_type`,`repeat_id`,`room_id`,`timestamp`,`create_by`,`beneficiaire_ext`,`beneficiaire`,`name`,`type`,`description`,`statut_entry`)
VALUES(:start_time,:end_time,:entry_type,:repeat_id,:room_id,:timestamp,:create_by,:beneficiaire_ext,:beneficiaire,:name,:type,:description,:statut_entry)';

            $statement = $pdo->prepare($sql);

            try {
                $statement->execute($data);
            } catch (\Exception $exception) {
                dump($data);
                dump($statement->errorInfo());
                dump($exception->getMessage());

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    private function getRoomId(Entry $entry): int
    {
        $t = match ($entry->getRoom()->getId()) {
            1 => 3,//Salle blanche
            2 => 2,
            3 => 3,
            4 => 4,
            default => 0
        };

        return $t;
    }
}
