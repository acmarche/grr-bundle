<?php

namespace Grr\GrrBundle\Controller\Front;

use DateTime;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Entry\Message\EntryCreated;
use Grr\Core\Entry\Message\EntryDeleted;
use Grr\Core\Entry\Message\EntryInitialized;
use Grr\Core\Entry\Message\EntryUpdated;
use Grr\Core\Router\FrontRouterHelper;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Factory\EntryFactory;
use Grr\GrrBundle\Entry\Form\EntryType;
use Grr\GrrBundle\Entry\Form\EntryWithPeriodicityType;
use Grr\GrrBundle\Entry\Form\SearchEntryType;
use Grr\GrrBundle\Entry\HandlerEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route(path: '/front/entry')]
class EntryController extends AbstractController
{
    public function __construct(
        private EntryFactory $entryFactory,
        private EntryRepositoryInterface $entryRepository,
        private HandlerEntry $handlerEntry,
        private EventDispatcherInterface $eventDispatcher,
        private FrontRouterHelper $frontRouterHelper,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'grr_front_entry_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $entries = [];
        $search = false;
        $today = new DateTime();
        $today->modify('-1 month');
        $args = [
            'startDate' => $today,
            'endDate' => new DateTime(),
        ];
        $form = $this->createForm(SearchEntryType::class, $args);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $entries = $this->entryRepository->search($args);
            $search = true;
        }

        return $this->render(
            '@grr_front/entry/index.html.twig',
            [
                'entries' => $entries,
                'search' => $search,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/new/area/{area}/room/{room}/date/{date}/hour/{hour}/minute/{minute}', name: 'grr_front_entry_new', methods: ['GET', 'POST'])]
    #[Entity(data: 'area', expr: 'repository.find(area)')]
    #[Entity(data: 'room', expr: 'repository.find(room)')]
    #[IsGranted('grr.addEntry', subject: 'room')]
    public function new(Request $request, Area $area, Room $room, \DateTime|\DateTimeImmutable $date, int $hour, int $minute): Response
    {
        $entry = $this->entryFactory->initEntryForNew($area, $room, $date, $hour, $minute);
        //bug
        //  $this->dispatchMessage(new EntryInitialized($entry->getId()));
        $form = $this->createForm(EntryWithPeriodicityType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerEntry->handleNewEntry($entry);

            $this->messageBus->dispatch(new EntryCreated($entry->getId()));

            return $this->redirectToRoute('grr_front_entry_show', [
                'id' => $entry->getId(),
            ]);
        }

        return $this->render(
            '@grr_front/entry/new.html.twig',
            [
                'entry' => $entry,
                'periodicity' => null,
                'displayOptionsWeek' => false,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_front_entry_show', methods: ['GET'])]
    #[IsGranted('grr.entry.show', subject: 'entry')]
    public function show(Entry $entry): Response
    {
        $urlList = $this->frontRouterHelper->generateMonthView($entry);
        $repeats = [];
        if (null !== ($periodicity = $entry->getPeriodicity())) {
            $repeats = $this->entryRepository->findByPeriodicity($periodicity);
        }

        return $this->render(
            '@grr_front/entry/show.html.twig',
            [
                'entry' => $entry,
                'repeats' => $repeats,
                'url_back' => $urlList,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'grr_front_entry_edit', methods: ['GET', 'POST'])]
    #[IsGranted('grr.entry.edit', subject: 'entry')]
    public function edit(Request $request, Entry $entry): Response
    {
        $entry->setArea($entry->getRoom()->getArea());
        if (null !== ($periodicity = $entry->getPeriodicity())) {
            $periodicity->setEntryReference($entry); //use for validator
        }
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerEntry->handleEditEntry();

            $this->messageBus->dispatch(new EntryUpdated($entry->getId()));

            return $this->redirectToRoute(
                'grr_front_entry_show',
                [
                    'id' => $entry->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_front/entry/edit.html.twig',
            [
                'entry' => $entry,
                'repeats' => [],
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_front_entry_delete', methods: ['POST'])]
    #[IsGranted('grr.entry.delete', subject: 'entry')]
    public function delete(Request $request, Entry $entry): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$entry->getId(), $request->request->get('_token'))) {
            $this->messageBus->dispatch(new EntryDeleted($entry->getId()));
            $this->handlerEntry->handleDeleteEntry($entry);
        }

        return $this->redirectToRoute('grr_homepage');
    }
}
