<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\GrrBundle\Entity\EntryType;
use Grr\Core\Events\EntryTypeEvent;
use Grr\GrrBundle\Form\TypeEntryType;
use Grr\GrrBundle\Manager\TypeEntryManager;
use Grr\GrrBundle\Repository\EntryTypeRepository;
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
class EntryTypeController extends AbstractController
{
    /**
     * @var EntryTypeRepository
     */
    private $entryTypeRepository;
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
        EntryTypeRepository $entryTypeRepository,
        TypeEntryManager $typeEntryManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entryTypeRepository = $entryTypeRepository;
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
                'type_entries' => $this->entryTypeRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="grr_admin_type_entry_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $entryType = $this->typeEntryFactory->createNew();

        $form = $this->createForm(TypeEntryType::class, $entryType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryManager->insert($entryType);

            $entryTypeEvent = new EntryTypeEvent($entryType);
            $this->eventDispatcher->dispatch($entryTypeEvent, EntryTypeEvent::NEW_SUCCESS);

            return $this->redirectToRoute('grr_admin_type_entry_index');
        }

        return $this->render(
            '@grr_admin/type_entry/new.html.twig',
            [
                'type_entry' => $entryType,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_type_entry_show", methods={"GET"})
     */
    public function show(EntryType $typeArea): Response
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
    public function edit(Request $request, EntryType $entryType): Response
    {
        $form = $this->createForm(TypeEntryType::class, $entryType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryManager->flush();

            $entryTypeEvent = new EntryTypeEvent($entryType);
            $this->eventDispatcher->dispatch($entryTypeEvent, EntryTypeEvent::EDIT_SUCCESS);

            return $this->redirectToRoute(
                'grr_admin_type_entry_index',
                [
                    'id' => $entryType->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/type_entry/edit.html.twig',
            [
                'type_entry' => $entryType,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_type_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntryType $entryType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entryType->getId(), $request->request->get('_token'))) {
            $this->typeEntryManager->remove($entryType);
            $this->typeEntryManager->flush();

            $entryTypeEvent = new EntryTypeEvent($entryType);
            $this->eventDispatcher->dispatch($entryTypeEvent, EntryTypeEvent::DELETE_SUCCESS);
        }

        return $this->redirectToRoute('grr_admin_type_entry_index');
    }
}
