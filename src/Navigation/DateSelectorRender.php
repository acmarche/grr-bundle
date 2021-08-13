<?php

namespace Grr\GrrBundle\Navigation;

use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DateSelectorRender
{
    private RequestStack $requestStack;
    private Environment $twigEnvironment;
    private DateProvider $dateProvider;
    private CarbonFactory $carbonFactory;

    public function __construct(
        RequestStack $requestStack,
        Environment $twigEnvironment,
        DateProvider $dateProvider,
        CarbonFactory $carbonFactory
    ) {
        $this->requestStack = $requestStack;
        $this->twigEnvironment = $twigEnvironment;
        $this->dateProvider = $dateProvider;
        $this->carbonFactory = $carbonFactory;
    }

    /**
     * @return Response|string
     *
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function render()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return new Response('');
        }

        $dateSelected = $request->get('date') ?? new DateTime();

        return $this->renderMonthByWeeks($dateSelected);
    }

    /**
     * @param DateTime|DateTimeImmutable $dateSelected
     */
    private function renderMonthByWeeks(DateTimeInterface $dateSelected): string
    {
        $today = Carbon::today();
        $dateSelected = $this->carbonFactory->instanceImmutable($dateSelected);
        $dateSelected->locale('fr');
        $weeks = $this->dateProvider->weeksOfMonth($dateSelected);

        $request = $this->requestStack->getMainRequest();
        $view = null !== $request ? $request->get('view') : null;

        return $this->twigEnvironment->render(
            '@grr_front/navigation/date_selector/_index.html.twig',
            [
                'today' => $today,
                'dateSelected' => $dateSelected,
                'weekdays' => $this->dateProvider->weekDaysName(),
                'weeks' => $weeks,
                'view' => $view,
            ]
        );
    }
}
