<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 18/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Periodicity;

use Exception;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\PeriodicityRepositoryInterface;
use Grr\Core\Periodicity\GeneratorEntry;
use Grr\Core\Periodicity\PeriodicityDaysProvider;

class HandlerPeriodicity
{
    public function __construct(
        private readonly PeriodicityRepositoryInterface $periodicityRepository,
        private readonly PeriodicityDaysProvider $periodicityDaysProvider,
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly GeneratorEntry $entryFactory
    ) {
    }

    public function handleNewPeriodicity(EntryInterface $entry): void
    {
        $periodicity = $entry->getPeriodicity();
        if (null !== $periodicity) {
            $days = $this->periodicityDaysProvider->getDaysByEntry($entry);
            foreach ($days as $day) {
                $newEntry = $this->entryFactory->generateEntry($entry, $day);
                $this->entryRepository->persist($newEntry);
            }
            $this->entryRepository->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function handleEditPeriodicity(EntryInterface $entry)
    {
        $periodicity = $entry->getPeriodicity();
        if (! $periodicity instanceof PeriodicityInterface) {
            return null;
        }

        $type = $periodicity->getType();

        /*
         * Si la périodicité mise sur 'aucune'
         */
        if (0 === $type || null === $type) {
            $entry->setPeriodicity(null);
            $this->entryRepository->removeEntriesByPeriodicity($periodicity, $entry);
            $this->periodicityRepository->remove($periodicity);
            $this->periodicityRepository->flush();

            return null;
        }

        /*
         * ici on supprime les entries de la periodicité mais on garde l'entry de base
         * et on reinjecte les nouvelles entries
         */
        $this->entryRepository->removeEntriesByPeriodicity($periodicity, $entry);
        $days = $this->periodicityDaysProvider->getDaysByEntry($entry);
        foreach ($days as $day) {
            $newEntry = $this->entryFactory->generateEntry($entry, $day);
            $this->entryRepository->persist($newEntry);
        }
        $this->entryRepository->flush();

        return null;
    }

    public function periodicityHasChange(EntryInterface $oldEntry, EntryInterface $entry): bool
    {
        if ($oldEntry->getStartTime() !== $entry->getStartTime()) {
            return true;
        }

        if ($oldEntry->getEndTime() !== $entry->getEndTime()) {
            return true;
        }

        $oldPeriodicity = $oldEntry->getPeriodicity();
        $periodicity = $entry->getPeriodicity();

        if (! $oldPeriodicity instanceof PeriodicityInterface || ! $periodicity instanceof PeriodicityInterface) {
            return true;
        }

        if ($oldPeriodicity->getEndTime() !== $periodicity->getEndTime()) {
            return true;
        }
        if ($oldPeriodicity->getType() !== $periodicity->getType()) {
            return true;
        }
        if ($oldPeriodicity->getWeekRepeat() !== $periodicity->getWeekRepeat()) {
            return true;
        }

        return $oldPeriodicity->getWeekDays() !== $periodicity->getWeekDays();
    }
}
