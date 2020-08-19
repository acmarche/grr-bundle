<?php

namespace Grr\GrrBundle\Area\Form;

use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\EventSubscriber\Form\AddRoomFieldSubscriber;
use Grr\GrrBundle\Navigation\MenuSelectDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaMenuSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'required' => true,
                ]
            )
            ->addEventSubscriber(new AddRoomFieldSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data' => MenuSelectDto::class,
            ]
        );
    }
}
