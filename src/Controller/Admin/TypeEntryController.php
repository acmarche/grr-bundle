<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\Core\TypeEntry\Message\TypeEntryCreated;
use Grr\Core\TypeEntry\Message\TypeEntryDeleted;
use Grr\Core\TypeEntry\Message\TypeEntryUpdated;
use Grr\GrrBundle\Entity\TypeEntry;
use Grr\GrrBundle\TypeEntry\Form\TypeEntryType;
use Grr\GrrBundle\TypeEntry\Manager\TypeEntryManager;
use Grr\GrrBundle\TypeEntry\TypeEntryFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/entrytype")
 * @IsGranted("ROLE_GRR_ADMINISTRATOR")
 */
class TypeEntryController extends AbstractController
{
    private TypeEntryRepositoryInterface $typeEntryRepository;
    private TypeEntryManager $typeEntryManager;
    private TypeEntryFactory $typeEntryFactory;

    public function __construct(
        TypeEntryFactory $typeEntryFactory,
        TypeEntryRepositoryInterface $typeEntryRepository,
        TypeEntryManager $typeEntryManager
    ) {
        $this->typeEntryRepository = $typeEntryRepository;
        $this->typeEntryManager = $typeEntryManager;
        $this->typeEntryFactory = $typeEntryFactory;
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

            $this->dispatchMessage(new TypeEntryCreated($typeEntry->getId()));

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
    public function show(TypeEntry $typeEntry): Response
    {
        return $this->render(
            '@grr_admin/type_entry/show.html.twig',
            [
                'type_entry' => $typeEntry,
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

            $this->dispatchMessage(new TypeEntryUpdated($typeEntry->getId()));

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
    public function delete(Request $request, TypeEntry $typeEntry): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $typeEntry->getId(), $request->request->get('_token'))) {
            $this->typeEntryManager->remove($typeEntry);
            $this->typeEntryManager->flush();

            $this->dispatchMessage(new TypeEntryDeleted($typeEntry->getId()));
        }

        return $this->redirectToRoute('grr_admin_type_entry_index');
    }
}
