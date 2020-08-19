<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 26/09/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Tests\Form;

use Carbon\CarbonInterface;
use Grr\GrrBundle\Area\Form\AreaType;
use Grr\GrrBundle\Entity\Area;
use Symfony\Component\Form\Test\TypeTestCase;

class AreaTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'name' => 'test',
            'isRestricted' => 0,
            'orderDisplay' => 1,
            'weekStart' => 0,
            'daysOfWeekToDisplay' => [CarbonInterface::MONDAY, CarbonInterface::SUNDAY, CarbonInterface::WEDNESDAY],
            'startTime' => 8,
            'endTime' => 19,
            'minutesToAddToEndTime' => 20,
            'timeInterval' => 30,
            'durationDefaultEntry' => 30,
            'durationMaximumEntry' => 0,
            'is24HourFormat' => true,
            'maxBooking' => 0,
        ];

        $objectToCompare = new Area();

        $form = $this->factory->create(AreaType::class, $objectToCompare);

        $area = new Area();
        $area->setName($formData['name']);
        $area->setIsRestricted($formData['isRestricted']);
        $area->setOrderDisplay($formData['orderDisplay']);
        $area->setWeekStart($formData['weekStart']);
        $area->setDaysOfWeekToDisplay($formData['daysOfWeekToDisplay']);
        $area->setStartTime($formData['startTime']);
        $area->setEndTime($formData['endTime']);
        $area->setMinutesToAddToEndTime($formData['minutesToAddToEndTime']);
        $area->setTimeInterval($formData['timeInterval']);
        $area->setDurationDefaultEntry($formData['durationDefaultEntry']);
        $area->setDurationMaximumEntry($formData['durationMaximumEntry']);
        $area->setIs24HourFormat($formData['is24HourFormat']);
        $area->setMaxBooking($formData['maxBooking']);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        // $this->assertEquals($area, $objectToCompare); //Uncaught Exception: Serialization of 'Closure' is not allowed in Standard input code

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
