<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\EventSubscriber\AddRoomFieldSubscriber;
use Grr\GrrBundle\Form\Type\AreaSelectType;
use Grr\GrrBundle\Navigation\MenuSelect;
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
                'data' => MenuSelect::class,
            ]
        );
    }
}
