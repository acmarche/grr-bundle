<?php

namespace Grr\GrrBundle\Area\Form;

use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Area;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType
{
    public function __construct(
        private readonly DateProvider $dateProvider
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $daysName = $this->dateProvider->weekDaysName();
        $formBuilder
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
                    'choices' => array_flip($daysName),
                    'label' => 'label.area.weekStart',
                ]
            )
            ->add(
                'daysOfWeekToDisplay',
                ChoiceType::class,
                [
                    'label' => 'label.area.displayDays',
                    'choices' => array_flip($daysName),
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'startTime',
                ChoiceType::class,
                [
                    'label' => 'label.area.startTime',
                    'choices' => $this->dateProvider->getHours(),
                ]
            )
            ->add(
                'endTime',
                ChoiceType::class,
                [
                    'label' => 'label.area.endTime',
                    'choices' => $this->dateProvider->getHours(),
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Area::class,
            ]
        );
    }
}
