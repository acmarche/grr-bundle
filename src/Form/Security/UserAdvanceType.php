<?php

namespace Grr\GrrBundle\Form\Security;

use Grr\Core\Security\SecurityRole;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdvanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'label' => 'label.user.roles.select',
                    'choices' => SecurityRole::getRoles(),
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'custom-control custom-checkbox my-1 mr-sm-2'],
                ]
            )
            ->add(
                'isEnabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'label.user.is_enabled',
                    'help' => 'help.user.is_enabled',
                ]
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

    public function getParent(): string
    {
        return UserType::class;
    }
}
