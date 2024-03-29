<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Contrat\Entity\PeriodicityInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Contrat\Repository\PeriodicityRepositoryInterface;
use Grr\Core\Periodicity\Message\PeriodicityDeleted;
use Grr\Core\Periodicity\Message\PeriodicityUpdated;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Periodicity;
use Grr\GrrBundle\Entry\Form\EntryWithPeriodicityType;
use Grr\GrrBundle\Entry\HandlerEntry;
use Grr\GrrBundle\Periodicity\PeriodicityConstant;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;


#[Route(path: '/periodicity')]
class PeriodicityController extends AbstractController
{
    public function __construct(
        private readonly PeriodicityRepositoryInterface $periodicityRepository,
        private readonly HandlerEntry $handlerEntry,
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/{id}/edit', name: 'grr_front_periodicity_edit', methods: ['GET', 'POST'])]
    #[IsGranted('grr.entry.edit', subject: 'entry')]
    public function edit(Request $request, Entry $entry): Response
    {
        $displayOptionsWeek = false;
        $entry = $this->handlerEntry->prepareToEditWithPeriodicity($entry);
        $periodicity = $entry->getPeriodicity();
        $typePeriodicity = $periodicity instanceof PeriodicityInterface ? $periodicity->getType() : 0;
        if (PeriodicityConstant::EVERY_WEEK === $typePeriodicity) {
            $displayOptionsWeek = true;
        }

        $oldEntry = clone $entry;
        $form = $this->createForm(EntryWithPeriodicityType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerEntry->handleEditEntryWithPeriodicity($oldEntry, $entry);
            $this->messageBus->dispatch(new PeriodicityUpdated($periodicity->getId()));

            return $this->redirectToRoute('grr_front_entry_show', [
                'id' => $entry->getId(),
            ]);
        }

        return $this->render(
            '@grr_front/periodicity/edit.html.twig',
            [
                'entry' => $entry,
                'periodicity' => $periodicity,
                'displayOptionsWeek' => $displayOptionsWeek,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'periodicity_delete', methods: ['POST'])]
    public function delete(Request $request, Periodicity $periodicity): RedirectResponse
    {
        $entry = $this->entryRepository->getBaseEntryForPeriodicity($periodicity);
        if ($this->isCsrfTokenValid('delete'.$periodicity->getId(), $request->request->get('_token'))) {
            $id = $periodicity->getId();
            foreach ($periodicity->getEntries() as $entry) {
                $this->periodicityRepository->remove($entry);
            }

            $this->periodicityRepository->remove($periodicity);
            $this->periodicityRepository->flush();

            $this->messageBus->dispatch(new PeriodicityDeleted($id));
        }

        return $this->redirectToRoute('grr_front_entry_show', [
            'id' => $entry->getId(),
        ]);
    }
}
