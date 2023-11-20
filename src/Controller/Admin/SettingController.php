<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Contrat\Repository\SettingRepositoryInterface;
use Grr\Core\Setting\Form\FormSettingFactory;
use Grr\Core\Setting\Message\SettingDeleted;
use Grr\Core\Setting\Message\SettingUpdated;
use Grr\Core\Setting\Repository\SettingProvider;
use Grr\GrrBundle\Entity\SettingEntity;
use Grr\GrrBundle\Setting\Handler\SettingHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/setting')]
#[IsGranted('ROLE_GRR_ADMINISTRATOR')]
class SettingController extends AbstractController
{
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
        private SettingHandler $settingHandler,
        private FormSettingFactory $formSettingFactory,
        private SettingProvider $settingProvider,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'grr_admin_setting_index', methods: ['GET'])]
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

    #[Route(path: '/edit', name: 'grr_admin_setting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $form = $this->formSettingFactory->generate();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->settingHandler->handleEdit($data);

            $this->messageBus->dispatch(new SettingUpdated([]));

            return $this->redirectToRoute('grr_admin_setting_index');
        }

        return $this->render(
            '@grr_admin/setting/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{name}', name: 'grr_admin_setting_delete', methods: ['POST'])]
    public function delete(Request $request, SettingEntity $setting): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$setting->getName(), $request->request->get('_token'))) {
            $this->settingRepository->remove($setting);
            $this->settingRepository->flush();
        }
        $this->messageBus->dispatch(new SettingDeleted([]));

        return $this->redirectToRoute('grr_admin_setting_index');
    }
}
