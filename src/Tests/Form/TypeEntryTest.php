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

use Grr\GrrBundle\Entity\TypeEntry;
use Grr\GrrBundle\TypeEntry\Form\TypeEntryType;
use Symfony\Component\Form\Test\TypeTestCase;

class TypeEntryTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'name' => 'test',
            'orderDisplay' => 1,
            'color' => '#FFFFFF',
            'letter' => 'A',
            'available' => 2,
        ];

        $objectToCompare = new TypeEntry();

        $form = $this->factory->create(TypeEntryType::class, $objectToCompare);

        $object = new TypeEntry();
        $object->setName($formData['name']);
        $object->setOrderDisplay($formData['orderDisplay']);
        $object->setColor($formData['color']);
        $object->setLetter($formData['letter']);
        $object->setAvailable($formData['available']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
