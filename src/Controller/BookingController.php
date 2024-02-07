<?php
/**
 * This file is part of sf5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 16/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Controller;

use DateTime;
use DateTimeImmutable;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Grr\Core\Factory\CarbonFactory;
use Grr\GrrBundle\Booking\ApiSerializer;
use Grr\GrrBundle\Booking\BookingForm;
use Grr\GrrBundle\Booking\BookingHandler;
use Grr\GrrBundle\Booking\Repository\BookingRepository;
use Grr\GrrBundle\Entity\Booking;
use Grr\GrrBundle\Entity\Room;
use Grr\GrrBundle\Entry\Form\EntryWithPeriodicityType;
use Grr\GrrBundle\Entry\HandlerEntry;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


#[Route(path: '/booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly EntryRepositoryInterface $entryRepository,
        private readonly CarbonFactory $carbonFactory,
        private readonly ApiSerializer $apiSerializer,
        private readonly BookingRepository $bookingRepository,
        private readonly BookingHandler $bookingHandler,
        private readonly HandlerEntry $handlerEntry
    ) {
    }

    #[Route(path: '/', name: 'grr_admin_booking_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@grr_admin/booking/index.html.twig',
            [
                'bookings' => $this->bookingRepository->findNotDone(),
            ]
        );
    }

    #[Route(path: '/{id}/show', name: 'grr_admin_booking_show', methods: ['GET'])]
    public function show(Booking $booking): Response
    {
        return $this->render(
            '@grr_admin/booking/show.html.twig',
            [
                'booking' => $booking,
            ]
        );
    }

    #[Route(path: '/new/{id}', name: 'grr_admin_entry_new_from_booking', methods: ['GET', 'POST'])]
    #[IsGranted('grr.addEntry')]
    public function new(Request $request, Booking $booking): Response
    {
        $entry = $this->bookingHandler->convertBookingToEntry($booking);
        $form = $this->createForm(EntryWithPeriodicityType::class, $entry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());
            $this->handlerEntry->handleNewEntry($entry);
            $this->bookingHandler->sendConfirmation($entry, $booking->getEmail());
            $booking->setDone(true);
            //$this->bookingRepository->flush();
            //$this->dispatchMessage(new EntryCreated($entry->getId()));

            //   return $this->redirectToRoute('grr_admin_booking_index');
        }

        return $this->render(
            '@grr_front/entry/new.html.twig',
            [
                'entry' => $entry,
                'periodicity' => null,
                'displayOptionsWeek' => false,
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'grr_admin_booking_delete', methods: ['POST'])]
    public function delete(Request $request, Booking $booking): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $this->bookingRepository->remove($booking);
            $this->bookingRepository->flush();

            $this->addFlash('success', 'Réservation supprimée');
        }

        return $this->redirectToRoute('grr_admin_booking_index');
    }

    #[Route(path: '/entries/{id}', methods: ['GET'])]
    public function entries(Room $room): JsonResponse
    {
        $today = $this->carbonFactory->today();
        $entries = $this->entryRepository->findForMonth($today->firstOfMonth(), null, $room);

        return $this->json($this->apiSerializer->serializeEntries($entries, false));
    }

    #[Route(path: '/entries/{date}/{id}', methods: ['GET'])]
    public function entriesByDate(DateTime|DateTimeImmutable $date, Room $room): JsonResponse
    {
        $today = $this->carbonFactory->instance($date);
        $entries = $this->entryRepository->findForDay($today, $room);

        return $this->json($this->apiSerializer->serializeEntries($entries, true));
    }

    #[Route(path: '/form', methods: ['GET'])]
    public function renderFormEntry(): Response
    {
        $form = $this->createForm(BookingForm::class);

        return $this->render('@Grr/front/api/_form.html.twig', [
            'form' => $form,
        ]);
    }
}
