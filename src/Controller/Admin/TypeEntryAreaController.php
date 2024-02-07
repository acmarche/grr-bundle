<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\TypeEntry\Message\TypeEntryAreaAssociated;
use Grr\GrrBundle\Area\Form\AssocTypeForAreaType;
use Grr\GrrBundle\Entity\Area;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;


#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/type/area')]
class TypeEntryAreaController extends AbstractController
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}/edit', name: 'grr_admin_type_area_edit', methods: ['GET', 'POST'])]
    #[IsGranted('grr.area.edit', subject: 'area')]
    public function edit(Request $request, Area $area): Response
    {
        $form = $this->createForm(AssocTypeForAreaType::class, $area);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaRepository->flush();

            $this->messageBus->dispatch(new TypeEntryAreaAssociated($area->getId()));

            return $this->redirectToRoute(
                'grr_admin_area_show',
                [
                    'id' => $area->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/type_area/edit.html.twig',
            [
                'area' => $area,
                'form' => $form,
            ]
        );
    }
}
