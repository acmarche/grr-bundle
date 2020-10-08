<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Periodicity\Message\PeriodicityDeleted;
use Grr\Core\Periodicity\Message\PeriodicityUpdated;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entry\Form\EntryWithPeriodicityType;
use Grr\GrrBundle\Entry\HandlerEntry;
use Grr\GrrBundle\Entry\Repository\EntryRepository;
use Grr\GrrBundle\Periodicity\Manager\PeriodicityManager;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
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
        EntryRepositoryInterface $entryRepository
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
            $this->dispatchMessage(new PeriodicityUpdated($periodicity->getId()));

            return $this->redirectToRoute('grr_front_entry_show', ['id' => $entry->getId()]);
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

            $this->dispatchMessage(new PeriodicityDeleted($periodicity->getId()));
        }

        return $this->redirectToRoute('grr_front_entry_show', ['id' => $entry->getId()]);
    }
}
