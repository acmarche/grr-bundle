<?php

namespace Grr\GrrBundle\Area\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Grr\GrrBundle\Area\Repository\AreaRepository;
use Grr\GrrBundle\Entity\Area;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'label' => 'label.area.select',
                'class' => Area::class,
                'query_builder' => static fn(AreaRepository $areaRepository): QueryBuilder => $areaRepository->getQueryBuilder(
                ),
                'attr' => [
                    'class' => 'my-1 mr-sm-2',
                ],
                'invalid_message' => 'The selected area does not exist',
            ]
        );
    }

    public function getParent(): ?string
    {
        return EntityType::class;
    }
}
