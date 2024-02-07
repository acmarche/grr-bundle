<?php

namespace Grr\GrrBundle\Controller\Admin;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


#[Route(path: '/admin')]
#[IsGranted('ROLE_GRR')]
class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'grr_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render(
            '@grr_admin/default/index.html.twig',
            [
            ]
        );
    }
}
