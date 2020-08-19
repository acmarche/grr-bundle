<?php

namespace Grr\GrrBundle\TypeEntry\Form;

use Grr\Core\Setting\SettingsTypeEntry;
use Grr\GrrBundle\Entity\EntryType;
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
                    'label' => 'label.typeEntry.name',
                ]
            )
            ->add(
                'orderDisplay',
                IntegerType::class,
                [
                    'label' => 'label.typeEntry.orderDisplay',
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                    'label' => 'label.typeEntry.color',
                ]
            )
            ->add(
                'letter',
                ChoiceType::class,
                [
                    'label' => 'label.typeEntry.letter',
                    'choices' => array_flip(SettingsTypeEntry::lettres()),
                ]
            )
            ->add(
                'available',
                ChoiceType::class,
                [
                    'label' => 'label.typeEntry.available',
                    'choices' => array_flip(SettingsTypeEntry::availableFor()),
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
