<?php

namespace Grr\GrrBundle\Controller\Admin;

use Grr\Core\Preference\Message\PreferenceUpdated;
use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\Preference\Factory\PreferenceFactory;
use Grr\GrrBundle\Preference\Form\EmailPreferenceType;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;


#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/preference')]
#[IsGranted('ROLE_GRR_MANAGER_USER')]
class EmailNotifcationPreferenceController extends AbstractController
{
    public function __construct(
        private readonly EmailPreferenceRepository $emailPreferenceRepository,
        private readonly PreferenceFactory $preferenceFactory,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'grr_admin_preference_email_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $preference = $this->preferenceFactory->createEmailPreferenceByUser($user);
        $form = $this->createForm(EmailPreferenceType::class, $preference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailPreferenceRepository->persist($preference);
            $this->emailPreferenceRepository->flush();

            $this->messageBus->dispatch(new PreferenceUpdated($preference->getId()));

            return $this->redirectToRoute('grr_admin_user_show', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render(
            '@grr_admin/preference/edit.html.twig',
            [
                'user' => $user,
                'form' => $form,
            ]
        );
    }
}
