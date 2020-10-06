<?php

namespace Grr\GrrBundle\Navigation;

use Carbon\Carbon;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DateSelectorRender
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Environment
     */
    private $twigEnvironment;

    public function __construct(
        RequestStack $requestStack,
        Environment $twigEnvironment
    ) {
        $this->requestStack = $requestStack;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|string
     *
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    public function render()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return new Response('');
        }

        $dateSelected = $request->get('date') ?? new \DateTime();

        return $this->renderMonthByWeeks($dateSelected);
    }

    private function renderMonthByWeeks(\DateTime $dateSelected): string
    {
        $today = Carbon::today();
        $dateSelected = Carbon::instance($dateSelected)->toImmutable();
        $weeks = DateProvider::weeksOfMonth($dateSelected);

        $request = $this->requestStack->getMasterRequest();
        $view = null !== $request ? $request->get('view') : null;

        return $this->twigEnvironment->render(
            '@grr_front/navigation/date_selector/_index.html.twig',
            [
                'today' => $today,
                'dateSelected' => $dateSelected,
                'listDays' => DateProvider::getNamesDaysOfWeek(),
                'weeks' => $weeks,
                'view' => $view,
            ]
        );
    }
}
