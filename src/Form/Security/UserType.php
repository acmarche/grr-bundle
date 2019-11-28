<?php

namespace Grr\GrrBundle\Form\Security;

use Grr\GrrBundle\Entity\Security\User;
use Grr\GrrBundle\EventSubscriber\AddRoomFieldSubscriber;
use Grr\GrrBundle\Form\Type\AreaSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'label.user.name',
                ]
            )
            ->add(
                'first_name',
                TextType::class,
                [
                    'label' => 'label.user.first_name',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'label.user.username',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'label' => 'label.user.area_select',
                    'required' => false,
                    'placeholder' => 'placeholder.area.select',
                ]
            )
            ->addEventSubscriber(
                new AddRoomFieldSubscriber(
                    false,
                    'label.user.room_select',
                    'placeholder.room.select_empty'
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
