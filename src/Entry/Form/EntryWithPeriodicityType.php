<?php

namespace Grr\GrrBundle\Entry\Form;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Periodicity\Form\PeriodicityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryWithPeriodicityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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

    public function getParent(): string
    {
        return EntryType::class;
    }
}
