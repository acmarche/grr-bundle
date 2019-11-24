<?php

namespace Grr\GrrBundle\Form;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\EntryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocTypeForAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'entryTypes',
                EntityType::class,
                [
                    'class' => EntryType::class,
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'typeEntry.form.name.label',
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
