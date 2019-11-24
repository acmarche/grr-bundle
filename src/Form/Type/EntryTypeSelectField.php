<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 27/02/19
 * Time: 21:57.
 */

namespace Grr\GrrBundle\Form\Type;

use Grr\GrrBundle\Entity\EntryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryTypeSelectField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'class' => EntryType::class,
                'label' => 'entry.form.type.label',
                'help' => 'entry.form.type.help',
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
