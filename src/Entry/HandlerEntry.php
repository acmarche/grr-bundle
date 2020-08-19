<?php

namespace Grr\GrrBundle\Entry;

use Grr\Core\Contrat\Entity\EntryInterface;
use Grr\Core\Service\PropertyUtil;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entry\Manager\EntryManager;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Periodicity\HandlerPeriodicity;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class HandlerEntry
{
    /**
     * @var EntryRepository
     */
    private $entryRepository;
    /**
     * @var EntryManager
     */
    private $entryManager;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var HandlerPeriodicity
     */
    private $handlerPeriodicity;
    /**
     * @var PropertyUtil
     */
    private $propertyUtil;

    public function __construct(
        EntryRepository $entryRepository,
        EntryManager $entryManager,
        HandlerPeriodicity $handlerPeriodicity,
        Security $security,
        PropertyUtil $propertyUtil
    ) {
        $this->entryRepository = $entryRepository;
        $this->entryManager = $entryManager;
        $this->security = $security;
        $this->handlerPeriodicity = $handlerPeriodicity;
        $this->propertyUtil = $propertyUtil;
    }

    public function handleNewEntry(FormInterface $form, EntryInterface $entry): void
    {
        $this->fullDay($entry);
        $periodicity = $entry->getPeriodicity();

        if (null !== $periodicity) {
            $type = $periodicity->getType();
            if (null === $type || 0 === $type) {
                $entry->setPeriodicity(null);
            }
        }

        $this->entryManager->insert($entry);
        $this->handlerPeriodicity->handleNewPeriodicity($entry);
    }

    public function handleEditEntry(): void
    {
        $this->entryManager->flush();
    }

    public function handleEditEntryWithPeriodicity(EntryInterface $oldEntry, EntryInterface $entry): void
    {
        if ($this->handlerPeriodicity->periodicityHasChange($oldEntry, $entry)) {
            $this->handlerPeriodicity->handleEditPeriodicity($oldEntry, $entry);
        } else {
            $this->updateEntriesWithSamePeriodicity($entry);
            $this->entryManager->flush();
        }
    }

    /**
     * todo set in service.
     */
    protected function fullDay(EntryInterface $entry): void
    {
        $duration = $entry->getDuration();
        if (null !== $duration) {
            if ($duration->isFullDay()) {
                $area = $entry->getArea();
                $hourStart = $area->getStartTime();
                $hourEnd = $area->getEndTime();

                $entry->getStartTime()->setTime($hourStart, 0);
                $entry->getEndTime()->setTime($hourEnd, 0);
            }
        }
    }

    public function handleDeleteEntry(EntryInterface $entry): void
    {
        $this->entryManager->remove($entry);
        $this->entryManager->flush();
    }

    protected function updateEntriesWithSamePeriodicity(EntryInterface $entry): void
    {
        $propertyAccessor = $this->propertyUtil->getPropertyAccessor();
        $excludes = ['id', 'createdAt'];

        foreach ($this->entryRepository->findByPeriodicity($entry->getPeriodicity()) as $entry2) {
            foreach ($this->propertyUtil->getProperties(Entry::class) as $property) {
                if (!in_array($property, $excludes, true)) {
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
