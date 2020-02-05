<?php
/**
 * This file is part of grr5 application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 27/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Templating\Helper;

use Grr\Core\Factory\MonthFactory;
use Grr\GrrBundle\Navigation\MenuGenerator;
use Grr\GrrBundle\Navigation\NavigationManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class NavigationHelper
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var MenuGenerator
     */
    private $menuGenerator;
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var NavigationManager
     */
    private $navigationManager;
    /**
     * @var MonthFactory
     */
    private $monthFactory;

    public function __construct(
        RequestStack $requestStack,
        MenuGenerator $menuGenerator,
        Environment $twigEnvironment,
        NavigationManager $navigationManager,
        MonthFactory $monthFactory
    ) {
        $this->requestStack = $requestStack;
        $this->menuGenerator = $menuGenerator;
        $this->twigEnvironment = $twigEnvironment;
        $this->navigationManager = $navigationManager;
        $this->monthFactory = $monthFactory;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|string
     *
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function monthNavigationRender()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return new Response('');
        }

        $year = $request->get('year') ?? 0;
        $month = $request->get('month') ?? 0;

        $monthModel = $this->monthFactory->create($year, $month);

        $navigation = $this->navigationManager->createMonth($monthModel);

        return $this->twigEnvironment->render(
            '@grr_front/navigation/month/_calendar_navigation.html.twig',
            [
                'navigation' => $navigation,
                'monthModel' => $monthModel,
            ]
        );
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function menuNavigationRender(): string
    {
        $form = $this->menuGenerator->generateMenuSelect();

        return $this->twigEnvironment->render(
            '@grr_front/navigation/form/_area_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
