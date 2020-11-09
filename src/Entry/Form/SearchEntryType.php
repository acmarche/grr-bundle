<?php

namespace Grr\GrrBundle\Entry\Form;

use Grr\GrrBundle\Area\Form\Type\AreaSelectType;
use Grr\GrrBundle\Entity\TypeEntry;
use Grr\GrrBundle\EventSubscriber\Form\AddRoomFieldSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'name',
                SearchType::class,
                [
                    'required' => false,
                    'label' => false,
                    'attr' => ['placeholder' => 'placeholder.keyword', 'class' => 'my-1 mr-sm-2'],
                ]
            )
            ->add(
                'type_entry',
                EntityType::class,
                [
                    'class' => TypeEntry::class,
                    'required' => false,
                    'label' => false,
                    'help' => null,
                    'placeholder' => 'placeholder.entryType.select',
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'required' => false,
                    'label' => false,
                    'placeholder' => 'placeholder.area.select',
                ]
            )
            ->addEventSubscriber(new AddRoomFieldSubscriber());
    }
}
