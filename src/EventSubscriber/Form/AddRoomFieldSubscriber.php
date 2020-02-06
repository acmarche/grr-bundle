<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 15/03/19
 * Time: 22:00.
 */

namespace Grr\GrrBundle\EventSubscriber\Form;

use Doctrine\ORM\QueryBuilder;
use Grr\GrrBundle\Form\Type\RoomSelectType;
use Grr\GrrBundle\Repository\RoomRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddRoomFieldSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $required;
    /**
     * @var string|null
     */
    private $label;
    /**
     * @var string|null
     */
    private $placeholder;

    public function __construct(
        bool $required = false,
        ?string $label = null,
        ?string $placeholder = null
    ) {
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;
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

    public function onPreSetData(FormEvent $event): void
    {
        $object = $event->getData();

        $area = is_array($object) ? null : $object->getArea();

        $form = $event->getForm();

        $default = [
            'required' => $this->required,
        ];

        if ($area) {
            $default['query_builder'] = function (RoomRepository $roomRepository) use ($area): QueryBuilder {
                return $roomRepository->getRoomsByAreaQueryBuilder($area);
            };
        } else {
            $default['choices'] = [];
        }

        if ($this->label) {
            $default['label'] = $this->label;
        }

        if ($this->placeholder) {
            $default['placeholder'] = $this->placeholder;
        }

        if (!$this->required) {
            $default['placeholder'] = 'room.form.select.placeholder';
        }

        $form->add(
            'room',
            RoomSelectType::class,
            $default
        );
    }
}
