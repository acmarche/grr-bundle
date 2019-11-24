<?php

namespace Grr\GrrBundle\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                SearchType::class,
                [
                    'required' => true,
                    'label' => 'user.form.search.name.label',
                    'help' => 'user.form.search.name.placeholder',
                    'attr' => ['placeholder' => 'user.form.search.name.placeholder', 'class' => 'm2y-1 hmr-smh-2'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            ]
        );
    }
}
