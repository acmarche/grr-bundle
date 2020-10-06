<?php

namespace Grr\GrrBundle\Navigation;

use Grr\GrrBundle\Area\Form\AreaMenuSelectType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

class AreaSelector
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var RessourceSelectedHelper
     */
    private $ressourceSelectedHelper;
    /**
     * @var MenuSelectFactory
     */
    private $menuSelectFactory;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(
        MenuSelectFactory $menuSelectFactory,
        FormFactoryInterface $formFactory,
        RessourceSelectedHelper $ressourceSelectedHelper,
        Environment $environment
    ) {
        $this->formFactory = $formFactory;
        $this->ressourceSelectedHelper = $ressourceSelectedHelper;
        $this->menuSelectFactory = $menuSelectFactory;
        $this->environment = $environment;
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(): string
    {
        $form = $this->generateMenuSelect();

        return $this->environment->render(
            '@grr_front/navigation/area_selector/_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    private function generateMenuSelect(): FormInterface
    {
        $area = $this->ressourceSelectedHelper->getArea();
        $room = $this->ressourceSelectedHelper->getRoom();

        $menuSelectDto = $this->menuSelectFactory->createNew();
        $menuSelectDto->setArea($area);
        $menuSelectDto->setRoom($room);

        return $this->formFactory->create(AreaMenuSelectType::class, $menuSelectDto);
    }

}
