<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\TypeEntry\Events\TypeEntryEventCreated;
use Grr\Core\TypeEntry\Events\TypeEntryEventDeleted;
use Grr\Core\TypeEntry\Events\TypeEntryEventUpdated;
use Grr\GrrBundle\Entity\TypeEntry;
use Grr\GrrBundle\TypeEntry\Form\TypeEntryType;
use Grr\GrrBundle\TypeEntry\Manager\TypeEntryManager;
use Grr\GrrBundle\TypeEntry\Repository\TypeEntryRepository;
use Grr\GrrBundle\TypeEntry\TypeEntryFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @Route("/admin/entrytype")
 * @IsGranted("ROLE_GRR_ADMINISTRATOR")
 */
class TypeEntryController extends AbstractController
{
    /**
     * @var TypeEntryRepository
     */
    private $typeEntryRepository;
    /**
     * @var TypeEntryManager
     */
    private $typeEntryManager;
    /**
     * @var TypeEntryFactory
     */
    private $typeEntryFactory;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        TypeEntryFactory $typeEntryFactory,
        TypeEntryRepository $typeEntryRepository,
        TypeEntryManager $typeEntryManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->typeEntryRepository = $typeEntryRepository;
        $this->typeEntryManager = $typeEntryManager;
        $this->typeEntryFactory = $typeEntryFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="grr_admin_type_entry_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@grr_admin/type_entry/index.html.twig',
            [
                'type_entries' => $this->typeEntryRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="grr_admin_type_entry_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $typeEntry = $this->typeEntryFactory->createNew();

        $form = $this->createForm(TypeEntryType::class, $typeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryManager->insert($typeEntry);

            $this->eventDispatcher->dispatch(new TypeEntryEventCreated($typeEntry));

            return $this->redirectToRoute('grr_admin_type_entry_index');
        }

        return $this->render(
            '@grr_admin/type_entry/new.html.twig',
            [
                'type_entry' => $typeEntry,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_type_entry_show", methods={"GET"})
     */
    public function show(TypeEntry $typeArea): Response
    {
        return $this->render(
            '@grr_admin/type_entry/show.html.twig',
            [
                'type_entry' => $typeArea,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="grr_admin_type_entry_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TypeEntry $typeEntry): Response
    {
        $form = $this->createForm(TypeEntryType::class, $typeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryManager->flush();

            $this->eventDispatcher->dispatch(new TypeEntryEventUpdated($typeEntry));

            return $this->redirectToRoute(
                'grr_admin_type_entry_index',
                [
                    'id' => $typeEntry->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/type_entry/edit.html.twig',
            [
                'type_entry' => $typeEntry,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_type_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TypeEntry $typeEntry): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeEntry->getId(), $request->request->get('_token'))) {
            $this->typeEntryManager->remove($typeEntry);
            $this->typeEntryManager->flush();

            $this->eventDispatcher->dispatch(new TypeEntryEventDeleted($typeEntry));
        }

        return $this->redirectToRoute('grr_admin_type_entry_index');
    }
}
