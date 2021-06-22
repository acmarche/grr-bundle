<?php

namespace Grr\GrrBundle\User\Form\Type;

use Grr\Core\Contrat\Repository\Security\UserRepositoryInterface;
use Grr\GrrBundle\Entity\Security\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectType extends AbstractType
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'label' => 'label.user.select',
                'class' => User::class,
                'multiple' => true,
                'expanded' => false,
                'query_builder' => $this->userRepository->getQueryBuilder(),
                'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
            ]
        );
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
