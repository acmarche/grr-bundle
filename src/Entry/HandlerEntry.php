<?php

namespace Grr\GrrBundle\Entry;

use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Model\DurationModel;
use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Service\PropertyUtil;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Periodicity\HandlerPeriodicity;

class HandlerEntry
{
    public function __construct(
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly HandlerPeriodicity $handlerPeriodicity,
        private readonly PropertyUtil $propertyUtil
    ) {
    }

    public function handleNewEntry(EntryInterface $entry): void
    {
        $this->fullDay($entry);
        $periodicity = $entry->getPeriodicity();

        if ($periodicity instanceof PeriodicityInterface) {
            $type = $periodicity->getType();
            if (null === $type || 0 === $type) {
                $entry->setPeriodicity(null);
            }
        }

        $this->entryRepository->persist($entry);
        $this->entryRepository->flush();

        $this->handlerPeriodicity->handleNewPeriodicity($entry);
    }

    public function handleEditEntry(): void
    {
        $this->entryRepository->flush();
    }

    public function handleEditEntryWithPeriodicity(EntryInterface $oldEntry, EntryInterface $entry): void
    {
        if ($this->handlerPeriodicity->periodicityHasChange($oldEntry, $entry)) {
            $this->handlerPeriodicity->handleEditPeriodicity($entry);
        } else {
            $this->updateEntriesWithSamePeriodicity($entry);
            $this->entryRepository->flush();
        }
    }

    /**
     * todo set in service.
     */
    protected function fullDay(EntryInterface $entry): void
    {
        $durationModel = $entry->getDuration();
        if ($durationModel instanceof DurationModel && $durationModel->isFullDay()) {
            $area = $entry->getArea();
            $startTime = $area->getStartTime();
            $endTime = $area->getEndTime();
            $entry->getStartTime()->setTime($startTime, 0);
            $entry->getEndTime()->setTime($endTime, 0);
        }
    }

    public function handleDeleteEntry(EntryInterface $entry): void
    {
        $this->entryRepository->remove($entry);
        $this->entryRepository->flush();
    }

    protected function updateEntriesWithSamePeriodicity(EntryInterface $entry): void
    {
        $propertyAccessor = $this->propertyUtil->getPropertyAccessor();
        $excludes = ['id', 'createdAt'];

        foreach ($this->entryRepository->findByPeriodicity($entry->getPeriodicity()) as $entry2) {
            foreach ($this->propertyUtil->getProperties(Entry::class) as $property) {
                if (! \in_array($property, $excludes, true)) {
                    $value = $propertyAccessor->getValue($entry, $property);
                    $propertyAccessor->setValue($entry2, $property, $value);
                }
            }
        }
    }

    public function prepareToEditWithPeriodicity(EntryInterface $entry): EntryInterface
    {
        $entryReference = $this->entryRepository->getBaseEntryForPeriodicity($entry->getPeriodicity());

        $entryReference->setArea($entryReference->getRoom()->getArea());

        $periodicity = $entryReference->getPeriodicity();
        $periodicity->setEntryReference($entryReference); //use for validator

        return $entryReference;
    }
}
