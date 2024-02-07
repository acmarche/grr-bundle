<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber\Form;

use Doctrine\ORM\QueryBuilder;
use Grr\GrrBundle\Room\Form\Type\RoomSelectType;
use Grr\GrrBundle\Room\Repository\RoomRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddRoomFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly bool $required = false,
        private ?string $label = null,
        private ?string $placeholder = null
    ) {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $formEvent): void
    {
        $object = $formEvent->getData();

        $area = \is_array($object) ? null : $object->getArea();

        $form = $formEvent->getForm();

        $default = [
            'required' => $this->required,
        ];

        if ($area) {
            $default['query_builder'] = fn (RoomRepository $roomRepository): QueryBuilder => $roomRepository->getRoomsByAreaQueryBuilder($area);
        } else {
            $default['choices'] = [];
        }

        if ($this->label) {
            $default['label'] = $this->label;
        }

        if ($this->placeholder) {
            $default['placeholder'] = $this->placeholder;
        }

        if (! $this->required) {
            $default['placeholder'] = 'room.form.select.placeholder';
        }

        $form->add(
            'room',
            RoomSelectType::class,
            $default
        );
    }
}
