<?php

namespace Grr\GrrBundle\Authorization\Form;

use Grr\GrrBundle\User\Form\Type\UserSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthorizationAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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

    public function getParent(): ?string
    {
        return AuthorizationType::class;
    }
}
