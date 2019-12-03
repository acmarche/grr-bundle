<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\Periodicity;
use Grr\Core\Periodicity\PeriodicityConstant;
use Grr\Core\Provider\DateProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodicityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = PeriodicityConstant::getTypesPeriodicite();
        $days = DateProvider::getNamesDaysOfWeek();
        $weeks = PeriodicityConstant::LIST_WEEKS_REPEAT;

        $builder
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
                ]
            )
            ->add(
                'weekDays',
                ChoiceType::class,
                [
                    'choices' => array_flip($days),
                    'label' => 'label.periodicity.week_days',
                    'help' => 'help.periodicity.week_days',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Periodicity::class,
            ]
        );
    }
}