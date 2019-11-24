<?php

namespace Grr\GrrBundle\Form\Security;

use Grr\GrrBundle\Form\Type\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorizationAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'users',
                UserSelectType::class,
                [
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
    }

    public function getParent(): string
    {
        return AuthorizationType::class;
    }
}
