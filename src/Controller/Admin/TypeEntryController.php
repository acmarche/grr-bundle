<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\TypeEntryRepositoryInterface;
use Grr\Core\TypeEntry\Message\TypeEntryCreated;
use Grr\Core\TypeEntry\Message\TypeEntryDeleted;
use Grr\Core\TypeEntry\Message\TypeEntryUpdated;
use Grr\GrrBundle\Entity\TypeEntry;
use Grr\GrrBundle\TypeEntry\Form\TypeEntryType;
use Grr\GrrBundle\TypeEntry\TypeEntryFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/entrytype')]
#[IsGranted(data: 'ROLE_GRR_ADMINISTRATOR')]
class TypeEntryController extends AbstractController
{
    public function __construct(
        private TypeEntryFactory $typeEntryFactory,
        private TypeEntryRepositoryInterface $typeEntryRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'grr_admin_type_entry_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@grr_admin/type_entry/index.html.twig',
            [
                'type_entries' => $this->typeEntryRepository->findAll(),
            ]
        );
    }

    #[Route(path: '/new', name: 'grr_admin_type_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $typeEntry = $this->typeEntryFactory->createNew();
        $form = $this->createForm(TypeEntryType::class, $typeEntry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryRepository->persist($typeEntry);
            $this->typeEntryRepository->flush();

            $this->messageBus->dispatch(new TypeEntryCreated($typeEntry->getId()));

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

    #[Route(path: '/{id}', name: 'grr_admin_type_entry_show', methods: ['GET'])]
    public function show(TypeEntry $typeEntry): Response
    {
        return $this->render(
            '@grr_admin/type_entry/show.html.twig',
            [
                'type_entry' => $typeEntry,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'grr_admin_type_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeEntry $typeEntry): Response
    {
        $form = $this->createForm(TypeEntryType::class, $typeEntry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->typeEntryRepository->flush();

            $this->messageBus->dispatch(new TypeEntryUpdated($typeEntry->getId()));

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

    #[Route(path: '/{id}', name: 'grr_admin_type_entry_delete', methods: ['POST'])]
    public function delete(Request $request, TypeEntry $typeEntry): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$typeEntry->getId(), $request->request->get('_token'))) {
            $this->typeEntryRepository->remove($typeEntry);
            $this->typeEntryRepository->flush();

            $this->messageBus->dispatch(new TypeEntryDeleted($typeEntry->getId()));
        }

        return $this->redirectToRoute('grr_admin_type_entry_index');
    }
}
