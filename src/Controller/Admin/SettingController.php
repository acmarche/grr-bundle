<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Setting\Events\SettingEventCreated;
use Grr\Core\Setting\Events\SettingEventDeleted;
use Grr\GrrBundle\Entity\Setting;
use Grr\GrrBundle\Form\GeneralSettingType;
use Grr\GrrBundle\Manager\SettingManager;
use Grr\GrrBundle\Repository\SettingRepository;
use Grr\GrrBundle\Setting\SettingHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/setting")
 * @IsGranted("ROLE_GRR_ADMINISTRATOR")
 */
class SettingController extends AbstractController
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;
    /**
     * @var SettingManager
     */
    private $settingManager;
    /**
     * @var SettingHandler
     */
    private $settingHandler;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        SettingManager $settingManager,
        SettingRepository $settingRepository,
        SettingHandler $settingHandler,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->settingRepository = $settingRepository;
        $this->settingManager = $settingManager;
        $this->settingHandler = $settingHandler;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="grr_admin_setting_index", methods={"GET"})
     */
    public function index(): Response
    {
        $settings = $this->settingRepository->findAll();

        return $this->render(
            '@grr_admin/setting/index.html.twig',
            [
                'settings' => $settings,
            ]
        );
    }

    /**
     * @Route("/edit", name="grr_admin_setting_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $settings = $this->settingRepository->load();
        $form = $this->createForm(GeneralSettingType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->settingHandler->handleEdit($data);

            $this->eventDispatcher->dispatch(new SettingEventCreated($settings));

            return $this->redirectToRoute('grr_admin_setting_index');
        }

        return $this->render(
            '@grr_admin/setting/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{name}", name="grr_admin_setting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Setting $setting): Response
    {
        if ($this->isCsrfTokenValid('delete' . $setting->getName(), $request->request->get('_token'))) {
            $this->settingManager->remove($setting);
            $this->settingManager->flush();
        }

        $this->eventDispatcher->dispatch(new SettingEventDeleted($setting));

        return $this->redirectToRoute('grr_admin_setting_index');
    }
}
