<?php

namespace Grr\GrrBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin')]
#[IsGranted(data: 'ROLE_GRR')]
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
