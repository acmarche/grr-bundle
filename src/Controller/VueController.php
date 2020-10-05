<?php

namespace Grr\GrrBundle\Controller;

use Grr\Core\Contrat\Repository\AreaRepositoryInterface;
use Grr\Core\Contrat\Repository\EntryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VueController extends AbstractController
{
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;

    public function __construct(EntryRepositoryInterface $entryRepository, AreaRepositoryInterface $areaRepository)
    {
        $this->entryRepository = $entryRepository;
        $this->areaRepository = $areaRepository;
    }

    /**
     * @Route("/vue", name="vue")
     */
    public function index()
    {
        return $this->render('vue/index.html.twig', [
            'areas' => $this->areaRepository->findAll(),
        ]);
    }
}
