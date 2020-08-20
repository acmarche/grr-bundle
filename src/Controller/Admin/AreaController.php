<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Area\Events\AreaEventCreated;
use Grr\Core\Area\Events\AreaEventDeleted;
use Grr\Core\Area\Events\AreaEventInitialized;
use Grr\Core\Area\Events\AreaEventUpdated;
use Grr\GrrBundle\Area\Factory\AreaFactory;
use Grr\GrrBundle\Area\Form\AreaType;
use Grr\GrrBundle\Area\Manager\AreaManager;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Authorization\Helper\AuthorizationHelper;
use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/area")
 */
class AreaController extends AbstractController
{
    /**
     * @var AreaFactory
     */
    private $areaFactory;
    /**
     * @var AreaManager
     */
    private $areaManager;
    /**
     * @var AreaRepository
     */
    private $areaRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;

    public function __construct(
        AreaFactory $areaFactory,
        AreaRepository $areaRepository,
        AreaManager $areaManager,
        RoomRepository $roomRepository,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationHelper $authorizationHelper
    ) {
        $this->areaFactory = $areaFactory;
        $this->areaManager = $areaManager;
        $this->areaRepository = $areaRepository;
        $this->roomRepository = $roomRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationHelper = $authorizationHelper;
    }

    /**
     * @Route("/", name="grr_admin_area_index", methods={"GET"})
     * @IsGranted("grr.area.index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $areas = $this->authorizationHelper->getAreasUserCanAdd($user);

        return $this->render(
            '@grr_admin/area/index.html.twig',
            [
                'areas' => $areas,
            ]
        );
    }

    /**
     * @Route("/new", name="grr_admin_area_new", methods={"GET", "POST"})
     * @IsGranted("grr.area.new")
     */
    public function new(Request $request): Response
    {
        $area = $this->areaFactory->createNew();

        $this->eventDispatcher->dispatch(new AreaEventInitialized($area));

        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaManager->insert($area);

            $this->eventDispatcher->dispatch(new AreaEventCreated($area));

            return $this->redirectToRoute('grr_admin_area_show', ['id' => $area->getId()]);
        }

        return $this->render(
            '@grr_admin/area/new.html.twig',
            [
                'area' => $area,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_area_show", methods={"GET"})
     * @IsGranted("grr.area.show", subject="area")
     */
    public function show(Area $area): Response
    {
        $rooms = $this->roomRepository->findBy(['area' => $area]);

        return $this->render(
            '@grr_admin/area/show.html.twig',
            [
                'area' => $area,
                'rooms' => $rooms,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="grr_admin_area_edit", methods={"GET", "POST"})
     * @IsGranted("grr.area.edit", subject="area")
     */
    public function edit(Request $request, Area $area): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->areaManager->flush();

            $this->eventDispatcher->dispatch(new AreaEventUpdated($area));

            return $this->redirectToRoute(
                'grr_admin_area_show',
                [
                    'id' => $area->getId(),
                ]
            );
        }

        return $this->render(
            '@grr_admin/area/edit.html.twig',
            [
                'area' => $area,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="grr_admin_area_delete", methods={"DELETE"})
     * @IsGranted("grr.area.delete", subject="area")
     */
    public function delete(Request $request, Area $area): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->request->get('_token'))) {
            $this->areaManager->removeRooms($area);
            $this->areaManager->remove($area);
            $this->areaManager->flush();

            $this->eventDispatcher->dispatch(new AreaEventDeleted($area));
        }

        return $this->redirectToRoute('grr_admin_area_index');
    }
}
