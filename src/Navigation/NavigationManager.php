<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 20/03/19
 * Time: 16:21.
 */

namespace Grr\GrrBundle\Navigation;

use Carbon\CarbonInterface;
use Grr\Core\Factory\MonthFactory;
use Grr\Core\Model\Month;
use Grr\Core\Model\Navigation;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Webmozart\Assert\Assert;

class NavigationManager
{
    /**
     * @var Environment
     */
    private $twigEnvironment;
    /**
     * @var Month
     */
    private $month;
    /**
     * @var CarbonInterface
     */
    private $carbon;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var NavigationFactory
     */
    private $navigationFactory;
    /**
     * @var MonthFactory
     */
    private $monthFactory;

    public function __construct(
        NavigationFactory $navigationFactory,
        Environment $environment,
        RequestStack $requestStack,
        MonthFactory $monthFactory
    ) {
        $this->twigEnvironment = $environment;
        $this->requestStack = $requestStack;
        $this->navigationFactory = $navigationFactory;
        $this->monthFactory = $monthFactory;
    }

    /**
     * @param int $number nombre de mois
     */
    public function createMonth(Month $month, int $number = 1): Navigation
    {
        $this->month = $month;

        Assert::greaterThan($number, 0);

        $navigation = $this->navigationFactory->createNew();
        $this->carbon = $navigation->getToday();

        $navigation->setNextButton($this->nextButtonRender());
        $navigation->setPreviousButton($this->previousButtonRender());

        $current = $this->month->firstOfMonth();

        for ($i = 0; $i < $number; ++$i) {
            $monthModel = $this->monthFactory->create($current->year, $current->month);
            $navigation->addMonth($this->renderMonthByWeeks($monthModel));
            $current->addMonth();
        }

        return $navigation;
    }

    public function previousButtonRender(): string
    {
        return $this->twigEnvironment->render(
            '@grr_front/navigation/month/_button_previous.html.twig',
            [
                'month' => $this->month,
            ]
        );
    }

    public function nextButtonRender(): string
    {
        return $this->twigEnvironment->render(
            '@grr_front/navigation/month/_button_next.html.twig',
            [
                'month' => $this->month,
            ]
        );
    }

    public function renderMonthByWeeks(Month $month): string
    {
        $firstDay = $month->firstOfMonth();
        /*  $year = $request->get('year') ?? 0;
          $month = $request->get('month') ?? 0;

          $monthModel = Month::init($year, $month);*/

        //  $navigation = $this->createMonth($month);

        $weeks = $this->month->getWeeksOfMonth();
        $request = $this->requestStack->getMasterRequest();
        $weekSelected = null !== $request ? $request->get('week') : 0;
        $daySelected = null !== $request ? $request->get('day') : 0;

        return $this->twigEnvironment->render(
            '@grr_front/navigation/month/_month_by_weeks.html.twig',
            [
                'firstDay' => $firstDay,
                'listDays' => DateProvider::getNamesDaysOfWeek(),
                'weeks' => $weeks,
                'weekSelected' => $weekSelected,
                'daySelected' => $daySelected,
                'today' => $this->carbon,
                'previousButton' => $this->previousButtonRender(),
                'nextButton' => $this->nextButtonRender(),
            ]
        );
    }
}
