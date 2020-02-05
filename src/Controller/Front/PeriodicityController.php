<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Events\EntryEvent;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entry\HandlerEntry;
use Grr\GrrBundle\Form\EntryWithPeriodicityType;
use Grr\GrrBundle\Manager\PeriodicityManager;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Grr\GrrBundle\Repository\EntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/periodicity")
 */
class PeriodicityController extends AbstractController
{
    /**
     * @var PeriodicityManager
     */
    private $periodicityManager;
    /**
     * @var HandlerEntry
     */
    private $handlerEntry;
    /**
     * @var EntryRepository
     */
    private $entryRepository;

    public function __construct(
        PeriodicityManager $periodicityManager,
        HandlerEntry $handlerEntry,
        EntryRepository $entryRepository
    ) {
        $this->periodicityManager = $periodicityManager;
        $this->handlerEntry = $handlerEntry;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @Route("/{id}/edit", name="grr_front_periodicity_edit", methods={"GET", "POST"})
     * @IsGranted("grr.entry.edit", subject="entry")
     */
    public function edit(Request $request, Entry $entry): Response
    {
        $displayOptionsWeek = false;
        $entry = $this->handlerEntry->prepareToEditWithPeriodicity($entry);

        $periodicity = $entry->getPeriodicity();
        $typePeriodicity = null !== $periodicity ? $periodicity->getType() : 0;

        if (PeriodicityConstant::EVERY_WEEK === $typePeriodicity) {
            $displayOptionsWeek = true;
        }

        $oldEntry = clone $entry;

        $form = $this->createForm(EntryWithPeriodicityType::class, $entry);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerEntry->handleEditEntryWithPeriodicity($oldEntry, $entry);

            $entryEvent = new EntryEvent($entry);

            //$this->eventDispatcher->dispatch($entryEvent, EntryEvent::EDIT_SUCCESS);

            return $this->redirectToRoute(
                'grr_front_entry_show',
                ['id' => $entry->getId()]
            );
        }

        return $this->render(
            '@grr_front/periodicity/edit.html.twig',
            [
                'entry' => $entry,
                'displayOptionsWeek' => $displayOptionsWeek,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="periodicity_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Periodicity $periodicity): Response
    {
        $entry = $this->entryRepository->getBaseEntryForPeriodicity($periodicity);

        if ($this->isCsrfTokenValid('delete'.$periodicity->getId(), $request->request->get('_token'))) {
            $this->periodicityManager->remove($periodicity);
        }

        return $this->redirectToRoute('grr_front_entry_show', ['id' => $entry->getId()]);
    }
}
