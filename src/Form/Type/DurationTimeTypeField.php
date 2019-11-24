<?php

namespace Grr\GrrBundle\Form\Type;

use Grr\Core\Model\DurationModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DurationTimeTypeField extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = DurationModel::getUnitsTime();

        $builder
            ->add(
                'time',
                NumberType::class,
                [
                    'label' => 'entry.form.duration_time.label',
                    'scale' => 1,
                ]
            )
            ->add(
                'unit',
                ChoiceType::class,
                [
                    'choices' => array_flip($choices),
                    //  'label' => 'entry.form.duration_unit.label',
                    'label' => ' ',
                    'help' => 'entry.form.duration_unit.help',
                ]
            )
            ->add(
                'full_day',
                CheckboxType::class,
                [
                    'label' => 'entry.form.full_day.label',
                    'help' => 'entry.form.full_day.help',
                    'required' => false,
                ]
            );
    }
}
