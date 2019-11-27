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

use Grr\GrrBundle\Entity\EntryType;
use Grr\GrrBundle\Form\TypeEntryType;
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
            'disponible' => 2,
        ];

        $objectToCompare = new EntryType();

        $form = $this->factory->create(TypeEntryType::class, $objectToCompare);

        $object = new EntryType();
        $object->setName($formData['name']);
        $object->setOrderDisplay($formData['orderDisplay']);
        $object->setColor($formData['color']);
        $object->setLetter($formData['letter']);
        $object->setDisponible($formData['disponible']);

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
