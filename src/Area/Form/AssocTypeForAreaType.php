<?php

namespace Grr\GrrBundle\Area\Form;

use Grr\GrrBundle\Entity\Area;
use Grr\GrrBundle\Entity\TypeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssocTypeForAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'typesEntry',
                EntityType::class,
                [
                    'class' => TypeEntry::class,
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'label.area.entryTypes',
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
