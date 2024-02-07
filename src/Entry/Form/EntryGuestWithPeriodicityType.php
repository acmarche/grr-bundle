<?php

namespace Grr\GrrBundle\Entry\Form;

use Grr\Core\Factory\DurationFactory;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\EventSubscriber\Form\AddDurationFieldSubscriber;
use Grr\GrrBundle\Periodicity\Form\PeriodicityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryGuestWithPeriodicityType extends AbstractType
{
    public function __construct(
        private readonly DurationFactory $durationFactory
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'startTime',
                DateTimeType::class,
                [
                    'label' => 'label.entry.startTime',
                    'help' => 'help.entry.startTime',
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'label.entry.name',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'label.entry.description',
                    'required' => false,
                ]
            )
            ->addEventSubscriber(new AddDurationFieldSubscriber($this->durationFactory))
            ->add('periodicity', PeriodicityType::class);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Entry::class,
            ]
        );
    }
}
