<?php

namespace Grr\GrrBundle\Room\Form\Type;

use Grr\GrrBundle\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setDefaults(
                [
                    'label' => 'label.room.select',
                    'class' => Room::class,
                    'attr' => [
                        'class' => 'my-1 mr-sm-2 room-select',
                    ],
                ]
            );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
