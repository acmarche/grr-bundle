<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\EntryType;
use Grr\Core\Setting\SettingsTypeEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'typeEntry.form.name.label',
                ]
            )
            ->add(
                'orderDisplay',
                IntegerType::class,
                [
                    'label' => 'typeEntry.form.orderDisplay.label',
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'letter',
                ChoiceType::class,
                [
                    'label' => 'typeEntry.form.typeLetter.label',
                    'choices' => array_flip(SettingsTypeEntry::lettres()),
                ]
            )
            ->add(
                'disponible',
                ChoiceType::class,
                [
                    'label' => 'typeEntry.form.disponible.label',
                    'choices' => array_flip(SettingsTypeEntry::disponibleFor()),
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => EntryType::class,
            ]
        );
    }
}
