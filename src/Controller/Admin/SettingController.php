<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\Form\FormSettingFactory;
use Grr\Core\Setting\Message\SettingDeleted;
use Grr\Core\Setting\Message\SettingUpdated;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\SettingEntity;
use Grr\GrrBundle\Setting\Handler\SettingHandler;
use Grr\GrrBundle\Setting\Manager\SettingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/setting")
 * @IsGranted("ROLE_GRR_ADMINISTRATOR")
 */
class SettingController extends AbstractController
{
    private SettingRepositoryInterface $settingRepository;
    private SettingManager $settingManager;
    private SettingHandler $settingHandler;
    private FormSettingFactory $formSettingFactory;
    private SettingProvider $settingProvider;

    public function __construct(
        SettingManager $settingManager,
        SettingRepositoryInterface $settingRepository,
        SettingHandler $settingHandler,
        FormSettingFactory $formSettingFactory,
        SettingProvider $settingProvider
    ) {
        $this->settingRepository = $settingRepository;
        $this->settingManager = $settingManager;
        $this->settingHandler = $settingHandler;
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

            $this->dispatchMessage(new SettingUpdated([]));

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
    public function delete(Request $request, SettingEntity $setting): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $setting->getName(), $request->request->get('_token'))) {
            $this->settingManager->remove($setting);
            $this->settingManager->flush();
        }

        $this->dispatchMessage(new SettingDeleted([]));

        return $this->redirectToRoute('grr_admin_setting_index');
    }
}
