<?php

namespace Grr\GrrBundle\Periodicity\Form;

use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\Core\Provider\DateProvider;
use Grr\GrrBundle\Entity\Periodicity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodicityType extends AbstractType
{
    public function __construct(
        private readonly DateProvider $dateProvider
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $types = PeriodicityConstant::getTypesPeriodicite();
        $days = $this->dateProvider->weekDaysName();
        $weeks = PeriodicityConstant::LIST_WEEKS_REPEAT;

        $formBuilder
            ->add(
                'endTime',
                DateType::class,
                [
                    'label' => 'label.periodicity.end_time',
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'label.periodicity.type',
                    'choices' => array_flip($types),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => false,
                    'placeholder' => false,
                    'attr' => [
                        'data-periodicity-target' => 'listPeriodicity',
                    ],
                ]
            )
            ->add(
                'weekDays',
                ChoiceType::class,
                [
                    'choices' => array_flip($days),
                    'label' => 'label.periodicity.week_days',
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'weekRepeat',
                ChoiceType::class,
                [
                    'choices' => array_flip($weeks),
                    'label' => 'label.periodicity.week_repeat',
                    'required' => false,
                    'multiple' => false,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Periodicity::class,
            ]
        );
    }
}
