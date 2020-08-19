<?php

namespace Grr\GrrBundle\Entry\Form;

use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Periodicity\Form\PeriodicityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryWithPeriodicityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('periodicity', PeriodicityType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
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
