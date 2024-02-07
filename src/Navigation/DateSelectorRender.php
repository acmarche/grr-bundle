<?php

namespace Grr\GrrBundle\Navigation;

use DateTimeImmutable;
use Carbon\Carbon;
use DateTime;
use Grr\Core\Factory\CarbonFactory;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DateSelectorRender
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $twigEnvironment,
        private readonly DateProvider $dateProvider,
        private readonly CarbonFactory $carbonFactory
    ) {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function render(): Response|string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request instanceof Request) {
            return new Response('');
        }

        $dateSelected = $request->get('date') ?? new DateTime();
        if (!$dateSelected instanceof DateTime) {

            $dateSelected = DateTime::createFromFormat('Y-m-d', $dateSelected);
        }

        return $this->renderMonthByWeeks($dateSelected);
    }

    private function renderMonthByWeeks(DateTime|DateTimeImmutable $dateSelected): string
    {
        $today = Carbon::today();
        $dateSelected = $this->carbonFactory->instanceImmutable($dateSelected);
        $dateSelected->locale('fr');

        $weeks = $this->dateProvider->weeksOfMonth($dateSelected);
        $request = $this->requestStack->getMainRequest();
        $view = $request instanceof Request ? $request->get('view') : null;

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
