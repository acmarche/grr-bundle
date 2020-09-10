<?php

namespace Grr\GrrBundle\Preference\Form;

use Grr\GrrBundle\Entity\Preference\EmailPreference;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailPreferenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'onCreated',
                CheckboxType::class,
                [
                    'label' => 'label.preference.email.onCreated',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'onUpdated',
                CheckboxType::class,
                [
                    'label' => 'label.preference.email.onUpdated',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            )
            ->add(
                'onDeleted',
                CheckboxType::class,
                [
                    'label' => 'label.preference.email.onDeleted',
                    'required' => false,
                    'label_attr' => [
                        'class' => 'switch-custom',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => EmailPreference::class,
            ]
        );
    }
}
