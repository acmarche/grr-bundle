<?php

namespace Grr\GrrBundle\Entry\Form\Type;

use Grr\Core\Model\DurationModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DurationTimeTypeField extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $choices = DurationModel::getUnitsTime();

        $formBuilder
            ->add(
                'time',
                NumberType::class,
                [
                    'label' => 'label.entry.duration_time',
                    'scale' => 1,
                ]
            )
            ->add(
                'unit',
                ChoiceType::class,
                [
                    'choices' => array_flip($choices),
                    'label' => ' ',
                    'help' => 'help.entry.duration_unit',
                ]
            )
            ->add(
                'full_day',
                CheckboxType::class,
                [
                    'label' => 'label.entry.full_day',
                    'required' => false,
                ]
            );
    }
}
