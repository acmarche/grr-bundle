<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\Security\AuthorizationRepositoryInterface;
use Grr\Core\Model\AuthorizationModel;
use Grr\GrrBundle\Authorization\Form\AuthorizationAreaType;
use Grr\GrrBundle\Authorization\Handler\HandlerAuthorization;
use Grr\GrrBundle\Entity\Area;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/authorization/area")
 */
class AuthorizationAreaController extends AbstractController
{
    private HandlerAuthorization $handlerAuthorization;
    private AuthorizationRepositoryInterface $authorizationRepository;

    public function __construct(
        HandlerAuthorization $handlerAuthorization,
        AuthorizationRepositoryInterface $authorizationRepository
    ) {
        $this->handlerAuthorization = $handlerAuthorization;
        $this->authorizationRepository = $authorizationRepository;
    }

    /**
     * @Route("/new/area/{id}", name="grr_authorization_from_area", methods={"GET", "POST"})
     *
     * @IsGranted("grr.area.edit", subject="area")
     */
    public function new(Request $request, Area $area = null): Response
    {
        $authorizationModel = new AuthorizationModel();

        if (null !== $area) {
            $authorizationModel->setArea($area);
        }

        $form = $this->createForm(AuthorizationAreaType::class, $authorizationModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handlerAuthorization->handle($form);

            if (null !== $area) {
                return $this->redirectToRoute('grr_authorization_area_show', ['id' => $area->getId()]);
            }
        }

        return $this->render(
            '@grr_admin/authorization/area/new.html.twig',
            [
                'area' => $area,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_authorization_area_show", methods={"GET"})
     * @IsGranted("grr.area.edit", subject="area")
     */
    public function show(Area $area): Response
    {
        $authorizations = $this->authorizationRepository->findByArea($area);
        $urlBack = $this->generateUrl('grr_authorization_show_by_user', ['id' => $area->getId()]);

        return $this->render(
            '@grr_admin/authorization/area/show.html.twig',
            [
                'area' => $area,
                'authorizations' => $authorizations,
                'url_back' => $urlBack,
            ]
        );
    }
}
