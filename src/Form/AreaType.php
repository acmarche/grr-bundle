<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\Area;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'label.area.name',
                ]
            )
            ->add(
                'isRestricted',
                CheckboxType::class,
                [
                    'label' => 'label.area.isRestricted',
                    'help' => 'help.area.isRestricted',
                    'required' => false,
                ]
            )
            ->add(
                'orderDisplay',
                IntegerType::class,
                [
                    'label' => 'label.area.orderDisplay',
                ]
            )
            ->add(
                'weekStart',
                ChoiceType::class,
                [
                    'choices' => array_flip(DateProvider::getNamesDaysOfWeek()),
                    'label' => 'label.area.weekStart',
                ]
            )
            ->add(
                'daysOfWeekToDisplay',
                ChoiceType::class,
                [
                    'label' => 'label.area.displayDays',
                    'choices' => array_flip(DateProvider::getNamesDaysOfWeek()),
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'startTime',
                ChoiceType::class,
                [
                    'label' => 'label.area.startTime',
                    'choices' => DateProvider::getHours(),
                ]
            )
            ->add(
                'endTime',
                ChoiceType::class,
                [
                    'label' => 'label.area.endTime',
                    'choices' => DateProvider::getHours(),
                ]
            )
            ->add(
                'minutesToAddToEndTime',
                IntegerType::class,
                [
                    'label' => 'label.area.minutesToAddToEndTime',
                ]
            )
            ->add(
                'timeInterval',
                IntegerType::class,
                [
                    'label' => 'label.area.timeInterval',
                    'help' => 'help.area.timeInterval',
                ]
            )
            ->add(
                'durationDefaultEntry',
                IntegerType::class,
                [
                    'label' => 'label.area.durationDefaultEntry',
                    'help' => 'help.area.durationDefaultEntry',
                ]
            )
            ->add(
                'durationMaximumEntry',
                IntegerType::class,
                [
                    'label' => 'label.area.durationMaximumEntry',
                    'help' => 'help.area.durationMaximumEntry',
                ]
            )
            ->add(
                'is24HourFormat',
                CheckboxType::class,
                [
                    'label' => 'label.area.is24HourFormat',
                ]
            )
            ->add(
                'maxBooking',
                IntegerType::class,
                [
                    'label' => 'label.area.maxBooking',
                    'help' => 'help.area.maxBooking',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Area::class,
            ]
        );
    }
}