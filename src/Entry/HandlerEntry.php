<?php

namespace Grr\GrrBundle\Entry;

use Grr\GrrBundle\Periodicity\HandlerPeriodicity;
use Grr\GrrBundle\Manager\EntryManager;
use Grr\GrrBundle\Repository\EntryRepository;
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

    public function handleNewEntry(FormInterface $form, Entry $entry): void
    {
        $this->setUserAdd($entry);
        $this->fullDay($entry);
        $periodicity = $entry->getPeriodicity();

        if ($periodicity !== null) {
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

    public function handleEditEntryWithPeriodicity(Entry $oldEntry, Entry $entry): void
    {
        if ($this->handlerPeriodicity->periodicityHasChange($oldEntry, $entry)) {
            $this->handlerPeriodicity->handleEditPeriodicity($oldEntry, $entry);
        } else {
            $this->updateEntriesWithSamePeriodicity($entry);
            $this->entryManager->flush();
        }
    }

    protected function fullDay(Entry $entry): void
    {
        $duration = $entry->getDuration();
        if ($duration !== null) {
            if ($duration->isFullDay()) {
                $area = $entry->getArea();
                $hourStart = $area->getStartTime();
                $hourEnd = $area->getEndTime();

                $entry->getStartTime()->setTime($hourStart, 0);
                $entry->getEndTime()->setTime($hourEnd, 0);
            }
        }
    }

    public function handleDeleteEntry(Entry $entry): void
    {
        $this->entryManager->remove($entry);
        $this->entryManager->flush();
    }

    protected function setUserAdd(Entry $entry): void
    {
        $user = $this->security->getUser();
        $username = isset($user) ? $user->getUsername() : null;
        $entry->setCreatedBy($username);
    }

    protected function updateEntriesWithSamePeriodicity(Entry $entry): void
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

    public function prepareToEditWithPeriodicity(Entry $entry): Entry
    {
        $entryReference = $this->entryRepository->getBaseEntryForPeriodicity($entry->getPeriodicity());

        $entryReference->setArea($entryReference->getRoom()->getArea());
        $periodicity = $entryReference->getPeriodicity();
        $periodicity->setEntryReference($entryReference); //use for validator

        return $entryReference;
    }
}
