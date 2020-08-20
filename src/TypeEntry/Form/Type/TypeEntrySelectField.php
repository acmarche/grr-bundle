<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/02/19
 * Time: 21:57.
 */

namespace Grr\GrrBundle\TypeEntry\Form\Type;

use Grr\GrrBundle\Entity\TypeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeEntrySelectField extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'class' => TypeEntry::class,
                'label' => 'label.entry.type_select',
                'help' => 'help.entry.type_select',
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
