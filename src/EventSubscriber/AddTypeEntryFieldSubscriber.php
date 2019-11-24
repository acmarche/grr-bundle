<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Form\Type\EntryTypeSelectField;
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

    public function onPreSetData(FormEvent $event): void
    {
        /**
         * @var Entry
         */
        $entry = $event->getData();
        $form = $event->getForm();
        $area = $entry->getArea();

        if ($area->getEntryTypes()->count() > 0) {
            $form->add(
                'type',
                EntryTypeSelectField::class,
                [
                    'choices' => $area->getEntryTypes(),
                ]
            );
        } else {
            $form->add(
                'type',
                EntryTypeSelectField::class,
                [
                ]
            );
        }
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $entry = $event->getData();
        $form = $event->getForm();

        if (!$entry) {
            return;
        }
    }
}
