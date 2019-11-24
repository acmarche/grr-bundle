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
                    'label' => 'user.form.name.label',
                ]
            )
            ->add(
                'first_name',
                TextType::class,
                [
                    'label' => 'user.form.first_name.label',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'user.form.username.label',
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
                    'label' => 'user.form.area.label',
                    'required' => false,
                    'placeholder' => 'area.form.select.placeholder',
                ]
            )
            ->addEventSubscriber(
                new AddRoomFieldSubscriber(
                    false,
                    'user.form.room.label',
                    'room.form.select.empty.placeholder'
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
