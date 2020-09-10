<?php

namespace Grr\GrrBundle\Controller\Front;

use Grr\GrrBundle\Preference\Factory\PreferenceFactory;
use Grr\GrrBundle\Preference\Form\EmailPreferenceType;
use Grr\GrrBundle\Preference\Manager\PreferenceManager;
use Grr\GrrBundle\Preference\Repository\EmailPreferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/front/preference")
 */
class PreferenceEmailController extends AbstractController
{
    /**
     * @var EmailPreferenceRepository
     */
    private $emailPreferenceRepository;
    /**
     * @var PreferenceFactory
     */
    private $preferenceFactory;
    /**
     * @var PreferenceManager
     */
    private $preferenceManager;

    public function __construct(
        EmailPreferenceRepository $emailPreferenceRepository,
        PreferenceFactory $preferenceFactory,
        PreferenceManager $preferenceManager
    ) {
        $this->emailPreferenceRepository = $emailPreferenceRepository;
        $this->preferenceFactory = $preferenceFactory;
        $this->preferenceManager = $preferenceManager;
    }

    /**
     * @Route("/show/{id}", name="grr_front_preference_email_show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render(
            '@grr_front/preference/show.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/edit", name="grr_front_preference_email_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $preference = $this->preferenceFactory->createEmailPreferenceByUser($user);

        $form = $this->createForm(EmailPreferenceType::class, $preference);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->preferenceManager->persist($preference);
            $this->preferenceManager->flush();
            return $this->redirectToRoute('grr_account_show');
        }

        return $this->render(
            '@grr_front/preference/edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }
}
