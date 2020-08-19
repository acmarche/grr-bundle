<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Area\Events\AreaEventAssociatedEntryType;
use Grr\GrrBundle\Area\Form\AssocTypeForAreaType;
use Grr\GrrBundle\Area\Manager\AreaManager;
use Grr\GrrBundle\Entity\Area;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type/area")
 */
class EntryTypeAreaController extends AbstractController
{
    /**
     * @var AreaManager
     */
    private $areaManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(AreaManager $areaManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->areaManager = $areaManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/{id}/edit", name="grr_admin_type_area_edit", methods={"GET", "POST"})
     * @IsGranted("grr.area.edit", subject="area")
     */
    public function edit(Request $request, Area $area): Response
    {
        $form = $this->createForm(AssocTypeForAreaType::class, $area);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaManager->flush();

            $this->eventDispatcher->dispatch(new AreaEventAssociatedEntryType($area));

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
                'form' => $form->createView(),
            ]
        );
    }
}
