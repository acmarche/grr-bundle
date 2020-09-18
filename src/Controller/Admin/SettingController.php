<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\Events\SettingEventCreated;
use Grr\Core\Setting\Events\SettingEventDeleted;
use Grr\Core\Setting\Form\FormSettingFactory;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\SettingEntity;
use Grr\GrrBundle\Setting\Handler\SettingHandler;
use Grr\GrrBundle\Setting\Manager\SettingManager;
use Grr\GrrBundle\Setting\Repository\SettingRepository;
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
    /**
     * @var FormSettingFactory
     */
    private $formSettingFactory;
    /**
     * @var SettingProvider
     */
    private $settingProvider;

    public function __construct(
        SettingManager $settingManager,
        SettingRepositoryInterface $settingRepository,
        SettingHandler $settingHandler,
        EventDispatcherInterface $eventDispatcher,
        FormSettingFactory $formSettingFactory,SettingProvider $settingProvider
    ) {
        $this->settingRepository = $settingRepository;
        $this->settingManager = $settingManager;
        $this->settingHandler = $settingHandler;
        $this->eventDispatcher = $eventDispatcher;
        $this->formSettingFactory = $formSettingFactory;
        $this->settingProvider = $settingProvider;
    }

    /**
     * @Route("/", name="grr_admin_setting_index", methods={"GET"})
     */
    public function index(): Response
    {
        $settings = $this->settingProvider->renderAll();

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
        $form = $this->formSettingFactory->generate();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->settingHandler->handleEdit($data);

            $this->eventDispatcher->dispatch(new SettingEventCreated([]));

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
    public function delete(Request $request, SettingEntity $setting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getName(), $request->request->get('_token'))) {
            $this->settingManager->remove($setting);
            $this->settingManager->flush();
        }

        $this->eventDispatcher->dispatch(new SettingEventDeleted([]));

        return $this->redirectToRoute('grr_admin_setting_index');
    }
}
