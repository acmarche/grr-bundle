<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\Core\Contrat\Entity\PeriodicityInterface;
use DateTime;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Entry\Message\EntryCreated;
use Grr\Core\Router\FrontRouterHelper;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Factory\EntryFactory;
use Grr\GrrBundle\Entry\Form\EntryGuestWithPeriodicityType;
use Grr\GrrBundle\Entry\HandlerEntry;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route(path: '/front/guest')]
class GuestController extends AbstractController
{
    public function __construct(
        private readonly EntryFactory $entryFactory,
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly HandlerEntry $handlerEntry,
        private readonly FrontRouterHelper $frontRouterHelper,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/new/room/{id}', name: 'grr_front_guest_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Room $room): Response
    {
        $area = $room->getArea();
        $date = new DateTime();
        $entry = $this->entryFactory->initEntryForNew($area, $room, $date, 8, 0);
        $form = $this->createForm(EntryGuestWithPeriodicityType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerEntry->handleNewEntry($entry);

            $this->messageBus->dispatch(new EntryCreated($entry->getId()));

            return $this->redirectToRoute('grr_front_entry_show', [
                'id' => $entry->getId(),
            ]);
        }

        return $this->render(
            '@grr_front/guest/new.html.twig',
            [
                'entry' => $entry,
                'room' => $room,
                'periodicity' => null,
                'displayOptionsWeek' => false,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'grr_front_guest_show', methods: ['GET'])]
    #[IsGranted('grr.entry.show', subject: 'entry')]
    public function show(Entry $entry): Response
    {
        $urlList = $this->frontRouterHelper->generateMonthView($entry);
        $repeats = [];
        if (($periodicity = $entry->getPeriodicity()) instanceof PeriodicityInterface) {
            $repeats = $this->entryRepository->findByPeriodicity($periodicity);
        }

        return $this->render(
            '@grr_front/guest/show.html.twig',
            [
                'entry' => $entry,
                'repeats' => $repeats,
                'url_back' => $urlList,
            ]
        );
    }
}
