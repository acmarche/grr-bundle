<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\TypeEntry\Message\TypeEntryAreaAssociated;
use Grr\GrrBundle\Area\Form\AssocTypeForAreaType;
use Grr\GrrBundle\Area\Manager\AreaManager;
use Grr\GrrBundle\Entity\Area;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type/area")
 */
class TypeEntryAreaController extends AbstractController
{
    /**
     * @var AreaManager
     */
    private $areaManager;

    public function __construct(AreaManager $areaManager)
    {
        $this->areaManager = $areaManager;
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

            $this->dispatchMessage(new TypeEntryAreaAssociated($area->getId()));

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
