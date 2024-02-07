<?php

namespace Grr\GrrBundle\Navigation;

use Grr\GrrBundle\Area\Form\AreaMenuSelectType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AreaSelector
{
    public function __construct(
        private readonly MenuSelectFactory $menuSelectFactory,
        private readonly FormFactoryInterface $formFactory,
        private readonly RessourceSelectedHelper $ressourceSelectedHelper,
        private readonly Environment $environment
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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
