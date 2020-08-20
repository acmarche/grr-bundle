<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber\Form;

use Grr\Core\Contrat\Entity\AreaInterface;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\TypeEntry\Form\Type\TypeEntrySelectField;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddTypeEntryFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSetData(FormEvent $formEvent): void
    {
        /**
         * @var Entry
         */
        $entry = $formEvent->getData();
        $form = $formEvent->getForm();
        /**
         * @var AreaInterface $area
         */
        $area = $entry->getArea();

        if ($area->getTypesEntry()->count() > 0) {
            $form->add(
                'type',
                TypeEntrySelectField::class,
                [
                    'choices' => $area->getTypesEntry(),
                ]
            );
        } else {
            $form->add(
                'type',
                TypeEntrySelectField::class,
                [
                ]
            );
        }
    }

    public function onPreSubmit(FormEvent $formEvent): void
    {
        $entry = $formEvent->getData();
        $form = $formEvent->getForm();

        if (!$entry) {
            return;
        }
    }
}
