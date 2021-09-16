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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/booking")
 */
class BookingController extends AbstractController
{
    private EntryRepositoryInterface $entryRepository;
    private CarbonFactory $carbonFactory;
    private ApiSerializer $apiSerializer;
    private BookingRepository $bookingRepository;
    private BookingHandler $bookingHandler;
    private HandlerEntry $handlerEntry;

    public function __construct(
        EntryRepositoryInterface $entryRepository,
        CarbonFactory $carbonFactory,
        ApiSerializer $apiSerializer,
        BookingRepository $bookingRepository,
        BookingHandler $bookingHandler,
        HandlerEntry $handlerEntry
    ) {
        $this->entryRepository = $entryRepository;
        $this->carbonFactory = $carbonFactory;
        $this->apiSerializer = $apiSerializer;
        $this->bookingRepository = $bookingRepository;
        $this->bookingHandler = $bookingHandler;
        $this->handlerEntry = $handlerEntry;
    }

    /**
     * @Route("/", name="grr_admin_booking_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@grr_admin/booking/index.html.twig',
            [
                'bookings' => $this->bookingRepository->findNotDone(),
            ]
        );
    }

    /**
     * @Route("/{id}/show", name="grr_admin_booking_show", methods={"GET"})
     */
    public function show(Booking $booking): Response
    {
        return $this->render(
            '@grr_admin/booking/show.html.twig',
            [
                'booking' => $booking,
            ]
        );
    }

    /**
     * @Route("/new/{id}", name="grr_admin_entry_new_from_booking", methods={"GET", "POST"})
     * @IsGranted("grr.addEntry")
     */
    public function new(
        Request $request,
        Booking $booking
    ): Response {
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
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/delete", name="grr_admin_booking_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Booking $booking): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $this->bookingRepository->remove($booking);
            $this->bookingRepository->flush();

            $this->addFlash('success', 'Réservation supprimée');
        }

        return $this->redirectToRoute('grr_admin_booking_index');
    }

    /**
     * @Route("/entries/{id}", methods={"GET"})
     */
    public function entries(Room $room): Response
    {
        $today = $this->carbonFactory->today();
        $entries = $this->entryRepository->findForMonth($today->firstOfMonth(), null, $room);

        return $this->json($this->apiSerializer->serializeEntries($entries, false));
    }

    /**
     * @Route("/entries/{date}/{id}", methods={"GET"})
     */
    public function entriesByDate(\DateTime $date, Room $room): Response
    {
        $today = $this->carbonFactory->instance($date);
        $entries = $this->entryRepository->findForDay($today, $room);

        return $this->json($this->apiSerializer->serializeEntries($entries, true));
    }

    /**
     * @Route("/form", methods={"GET"})
     */
    public function renderFormEntry(): Response
    {
        $form = $this->createForm(BookingForm::class);

        return $this->render('@Grr/front/api/_form.html.twig', ['form' => $form->createView()]);
    }

}
